<?php

declare(strict_types=1);

/**
 * MaxRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

namespace UnitTest\Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use Ares\Validation\Context;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Rule\MaxRule;
use Ares\Validation\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class MaxRuleTest
 *
 * @covers \Ares\Validation\Rule\AbstractRule
 * @coversDefaultClass \Ares\Validation\Rule\MaxRule
 */
class MaxRuleTest extends TestCase
{
    /**
     * @covers \Ares\Validation\Rule\MaxRule
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $maxRule = new MaxRule();

        $this->assertInstanceOf($fqcn, $maxRule);
    }

    /**
     * @covers ::getSupportedTypes
     *
     * @testWith ["float"]
     *           ["integer"]
     *           ["numeric"]
     *
     * @param string $type Supported type.
     * @return void
     */
    public function testGetSupportedTypes(string $type): void
    {
        $maxRule = new MaxRule();

        $this->assertContains($type, $maxRule->getSupportedTypes());
    }

    /**
     * @covers ::performValidation
     *
     * @testWith [null]
     *           ["foo"]
     *           [true]
     *           [false]
     *           [[]]
     *
     * @param mixed $args Validation rule configuration.
     * @return void
     */
    public function testValidateToHandleInvalidValidationRuleArgs($args): void
    {
        $data = 'foo';

        $context = new Context($data, new ErrorMessageRenderer());
        $context->enter('', (new Schema())->setRule(new Rule(TypeRule::ID, Type::INTEGER)));

        $maxRule = new MaxRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $maxRule->performValidation($args, $data, $context);
    }

    /**
     * @covers ::performValidation
     *
     * @dataProvider getValidateSamples
     *
     * @param string        $type           Schema value type.
     * @param integer|float $args           Validation rule configuration.
     * @param mixed         $data           Validated data.
     * @param bool          $expectedRetVal Expected validation return value.
     * @return void
     */
    public function testValidate($type, $args, $data, bool $expectedRetVal): void
    {
        $errorMessageRenderer = new ErrorMessageRenderer();

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, $errorMessageRenderer])
            ->onlyMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, $type))
                ->setRule(new Rule(MaxRule::ID, $args))
        );

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(MaxRule::ID, $errorMessageRenderer->render($context, MaxRule::ID, MaxRule::ERROR_MESSAGE, ['value' => $args]));
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $maxRule = new MaxRule();

        $this->assertSame($expectedRetVal, $maxRule->performValidation($args, $data, $context));
    }

    /**
     * @return array
     */
    public static function getValidateSamples(): array
    {
        return [
            'integer value too great' => [Type::INTEGER, 12, 13, false],
            'integer value is max value' => [Type::INTEGER, 12, 12, true],
            'integer value is smaller max value' => [Type::INTEGER, 12, 11, true],
            'float value too great' => [Type::FLOAT, 12.2, 13.3, false],
            'float value is max value' => [Type::FLOAT, 12.2, 12.2, true],
            'float value is smaller max value' => [Type::FLOAT, 12.2, 11.1, true],
        ];
    }

    /**
     * @covers ::performValidation
     *
     * @return void
     */
    public function testValidateHandlesCustomMessage(): void
    {
        $type = Type::INTEGER;
        $args = 23;
        $data = 25;
        $customMessage = 'The value of this field must not be greater than {value}';

        $errorMessageRenderer = new ErrorMessageRenderer();

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, $errorMessageRenderer])
            ->onlyMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, $type))
                ->setRule(new Rule(MaxRule::ID, $args, $customMessage))
        );

        $context->expects($this->once())
            ->method('addError')
            ->with(MaxRule::ID, $errorMessageRenderer->render($context, MaxRule::ID, $customMessage, ['value' => $args]));

        $maxRule = new MaxRule();

        $this->assertFalse($maxRule->performValidation($args, $data, $context));
    }
}
