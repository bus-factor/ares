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
use Ares\Exception\UnknownValidationRuleIdException;
use Ares\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidationTest
 *
 * @coversDefaultClass \Ares\Validator
 */
class ValidationTest extends TestCase
{
    /**
     * @covers ::validate
     * @covers ::performValidation
     * @covers ::getRule
     *
     * @return void
     */
    public function testValidateHandlesUnknownValidationRuleIds(): void
    {
        $schema = ['type' => 'string', 'uargh' => true];
        $validator = new Validator($schema);

        $this->expectException(UnknownValidationRuleIdException::class);
        $this->expectExceptionMessage('Unknown validation rule ID: uargh');

        $validator->validate('foobar');
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
     * @covers ::getErrorMessageRenderer
     *
     * @return void
     */
    public function testConstructorUsesProvidedErrorMessageRenderer(): void
    {
        $errorMessageRenderer = new class implements ErrorMessageRendererInterface {
            public function render(Context $context, string $ruleId, string $message, array $substitutions = []): string {
                return '';
            }
        };

        $validator = new Validator(['type' => 'integer'], [], $errorMessageRenderer);

        $this->assertSame($errorMessageRenderer, $validator->getErrorMessageRenderer());
    }
}

