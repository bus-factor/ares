<?php

declare(strict_types=1);

/**
 * NullableRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace UnitTest\Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use Ares\Validation\Context;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Rule\NullableRule;
use Ares\Validation\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class NullableRuleTest
 *
 * @coversDefaultClass \Ares\Validation\Rule\NullableRule
 */
class NullableRuleTest extends TestCase
{
    /**
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $nullableRule = new NullableRule();

        $this->assertInstanceOf($fqcn, $nullableRule);
    }

    /**
     * @covers ::getSupportedTypes
     *
     * @testWith ["boolean"]
     *           ["float"]
     *           ["integer"]
     *           ["list"]
     *           ["map"]
     *           ["numeric"]
     *           ["string"]
     *           ["tuple"]
     *
     * @param string $type Supported type.
     * @return void
     */
    public function testGetSupportedTypes(string $type): void
    {
        $nullableRule = new NullableRule();

        $this->assertContains($type, $nullableRule->getSupportedTypes());
    }

    /**
     * @covers ::isApplicable
     *
     * @return void
     */
    public function testIsApplicable(): void
    {
        $data = [];
        $nullableRule = new NullableRule();
        $context = new Context($data, new ErrorMessageRenderer());

        $this->assertTrue($nullableRule->isApplicable($context));
    }

    /**
     * @covers ::performValidation
     *
     * @testWith [1]
     *           [17.2]
     *           [null]
     *           ["foo"]
     *           [[]]
     *           [{}]
     *
     * @param mixed $args Validation rule configuration.
     * @return void
     */
    public function testValidateToHandleInvalidValidationRuleArgs($args): void
    {
        $data = 'foo';
        $context = new Context($data, new ErrorMessageRenderer());
        $nullableRule = new NullableRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $nullableRule->performValidation($args, $data, $context);
    }

    /**
     * @covers ::performValidation
     *
     * @testWith [true,  "",   true]
     *           [true,  3,    true]
     *           [true,  42.4, true]
     *           [true,  [],   true]
     *           [true,  {},   true]
     *           [true,  true, true]
     *           [false, "",   true]
     *           [false, 3,    true]
     *           [false, 42.4, true]
     *           [false, [],   true]
     *           [false, {},   true]
     *           [false, true, true]
     *           [true,  null, true]
     *           [false, null, false]
     *
     * @param bool  $args           Validation rule configuration.
     * @param mixed $data           Validated data.
     * @param bool  $expectedRetVal Expected validation return value.
     * @return void
     */
    public function testValidate(bool $args, $data, bool $expectedRetVal): void
    {
        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::INTEGER))
                ->setRule(new Rule(NullableRule::ID, $args))
        );

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(NullableRule::ID, NullableRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $nullableRule = new NullableRule();

        $this->assertSame($expectedRetVal, $nullableRule->performValidation($args, $data, $context));
    }

    /**
     * @covers ::performValidation
     *
     * @return void
     */
    public function testValidateHandlesCustomMessage(): void
    {
        $args = false;
        $data = null;
        $customMessage = 'Everything is allowed but NULL';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::INTEGER))
                ->setRule(new Rule(NullableRule::ID, $args, $customMessage))
        );

        $context->expects($this->once())
            ->method('addError')
            ->with(NullableRule::ID, $customMessage);

        $nullableRule = new NullableRule();

        $this->assertFalse($nullableRule->performValidation($args, $data, $context));
    }
}

