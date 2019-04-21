<?php

declare(strict_types=1);

/**
 * ValidatorTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-22
 */

namespace UnitTest\Ares;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use Ares\Error\ErrorMessageRendererInterface;
use Ares\Exception\InvalidValidationSchemaException;
use Ares\Exception\InvalidValidationOptionException;
use Ares\RuleFactory;
use Ares\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidatorTest
 *
 * @coversDefaultClass \Ares\Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @covers ::validate
     * @covers ::performValidation
     *
     * @return void
     */
    public function testValidateHandlesUnknownValidationRuleIds(): void
    {
        $this->expectException(InvalidValidationSchemaException::class);
        $this->expectExceptionMessage('Unknown validation rule ID: /uargh specifies an unknown validation rule ID');

        $schema = ['type' => 'string', 'uargh' => true];
        $validator = new Validator($schema);
    }

    /**
     * @covers ::__construct
     * @covers ::getErrorMessageRenderer
     * @covers ::setErrorMessageRenderer
     *
     * @return void
     */
    public function testErrorMessageRendererAccessors(): void
    {
        $validator = new Validator(['type' => 'integer']);

        $this->assertInstanceOf(ErrorMessageRenderer::class, $validator->getErrorMessageRenderer());

        $errorMessageRenderer = new class implements ErrorMessageRendererInterface {
            public function render(Context $context, string $ruleId, string $message, array $substitutions = []): string {
                return '';
            }
        };

        $this->assertSame($validator, $validator->setErrorMessageRenderer($errorMessageRenderer));
        $this->assertSame($errorMessageRenderer, $validator->getErrorMessageRenderer());
    }

    /**
     * @covers ::__construct
     * @covers ::getRuleFactory
     *
     * @return void
     */
    public function testConstructorUsesProvidedRuleFactory(): void
    {
        $ruleFactory = new RuleFactory();
        $validator = new Validator(['type' => 'integer'], [], $ruleFactory);

        $this->assertSame($ruleFactory, $validator->getRuleFactory());
    }

    /**
     * @covers ::__construct
     * @covers ::prepareOptions
     *
     * @testWith [{}, null]
     *           [{"allRequired": true}, null]
     *           [{"allBlankable": true}, null]
     *           [{"allNullable": true}, null]
     *           [{"allUnknownAllowed": true}, null]
     *           [{"allRequired": 13.37}, "Invalid validation option: 'allRequired' must be of type <boolean>, got <double>"]
     *           [{"allBlankable": 13.37},  "Invalid validation option: 'allBlankable' must be of type <boolean>, got <double>"]
     *           [{"allNullable": 13.37},  "Invalid validation option: 'allNullable' must be of type <boolean>, got <double>"]
     *           [{"allUnknownAllowed": 13.37},  "Invalid validation option: 'allUnknownAllowed' must be of type <boolean>, got <double>"]
     *           [{"foo": "bar"}, "Unknown validation option: 'foo' is not a supported validation option"]
     *
     * @param array       $options                  Validation options.
     * @param string|null $expectedExceptionMessage Expected exception message.
     * @return void
     */
    public function testConstructorHandlesInvalidOptions(
        array $options,
        ?string $expectedExceptionMessage = null
    ): void {
        if ($expectedExceptionMessage !== null) {
            $this->expectException(InvalidValidationOptionException::class);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $validator = new Validator(['type' => 'integer'], $options);

        $this->assertTrue($expectedExceptionMessage === null);
    }
}

