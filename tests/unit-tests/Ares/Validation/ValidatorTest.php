<?php

declare(strict_types=1);

/**
 * ValidatorTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-22
 */

namespace UnitTest\Ares\Validation;

use Ares\Exception\InvalidOptionException;
use Ares\Schema\Parser;
use Ares\Validation\Context;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Error\ErrorMessageRendererInterface;
use Ares\Validation\RuleFactory;
use Ares\Validation\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidatorTest
 *
 * @coversDefaultClass \Ares\Validation\Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getErrorMessageRenderer
     * @covers ::setErrorMessageRenderer
     *
     * @return void
     */
    public function testErrorMessageRendererAccessors(): void
    {
        $ruleFactory = new RuleFactory();
        $schemaParser = new Parser($ruleFactory);
        $schema = $schemaParser->parse(['type' => 'integer']);
        $validator = new Validator($schema, $ruleFactory);

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
        $schemaParser = new Parser($ruleFactory);
        $schema = $schemaParser->parse(['type' => 'integer']);
        $validator = new Validator($schema, $ruleFactory);

        $this->assertSame($ruleFactory, $validator->getRuleFactory());
    }

    /**
     * @covers ::__construct
     * @covers ::prepareOptions
     * @covers ::validate
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
    public function testValidateHandlesInvalidOptions(
        array $options,
        ?string $expectedExceptionMessage = null
    ): void {
        if ($expectedExceptionMessage !== null) {
            $this->expectException(InvalidOptionException::class);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $ruleFactory = new RuleFactory();
        $schemaParser = new Parser($ruleFactory);
        $schema = $schemaParser->parse(['type' => 'integer']);
        $validator = new Validator($schema, $ruleFactory);

        $validator->validate(1337, $options);

        $this->assertTrue($expectedExceptionMessage === null);
    }
}

