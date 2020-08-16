<?php

declare(strict_types=1);

/**
 * MinRuleTest.php
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
use Ares\Validation\Rule\MinRule;
use Ares\Validation\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class MinRuleTest
 *
 * @coversDefaultClass \Ares\Validation\Rule\MinRule
 */
class MinRuleTest extends TestCase
{
    /**
     * @covers \Ares\Validation\Rule\MinRule
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $minRule = new MinRule();

        $this->assertInstanceOf($fqcn, $minRule);
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
        $minRule = new MinRule();

        $this->assertContains($type, $minRule->getSupportedTypes());
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

        $minRule = new MinRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $minRule->performValidation($args, $data, $context);
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
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, $type))
                ->setRule(new Rule(MinRule::ID, $args))
        );

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(MinRule::ID, $errorMessageRenderer->render($context, MinRule::ID, MinRule::ERROR_MESSAGE, ['value' => $args]));
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $minRule = new MinRule();

        $this->assertSame($expectedRetVal, $minRule->performValidation($args, $data, $context));
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
    {
        return [
            'integer value too small' => [Type::INTEGER, 12, 11, false],
            'integer value is min value' => [Type::INTEGER, 12, 12, true],
            'integer value is greater min value' => [Type::INTEGER, 12, 13, true],
            'float value too small' => [Type::FLOAT, 12.2, 11.1, false],
            'float value is min value' => [Type::FLOAT, 12.2, 12.2, true],
            'float value is greater min value' => [Type::FLOAT, 12.2, 13.3, true],
        ];
    }

    /**
     * @covers ::performValidation
     *
     * @return void
     */
    public function testValidateHandlesCustomMessage(): void
    {
        $args = 23;
        $data = 20;
        $customMessage = 'The provided value must not be smaller than {value}';

        $errorMessageRenderer = new ErrorMessageRenderer();

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, $errorMessageRenderer])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::INTEGER))
                ->setRule(new Rule(MinRule::ID, $args, $customMessage))
        );

        $context->expects($this->once())
            ->method('addError')
            ->with(MinRule::ID, $errorMessageRenderer->render($context, MinRule::ID, $customMessage, ['value' => $args]));

        $minRule = new MinRule();

        $this->assertFalse($minRule->performValidation($args, $data, $context));
    }
}

