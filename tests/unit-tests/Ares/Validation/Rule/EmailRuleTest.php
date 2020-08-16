<?php

declare(strict_types=1);

/**
 * EmailRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

namespace UnitTest\Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use Ares\Validation\Context;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Rule\EmailRule;
use Ares\Validation\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class EmailRuleTest
 *
 * @coversDefaultClass \Ares\Validation\Rule\EmailRule
 */
class EmailRuleTest extends TestCase
{
    /**
     * @covers \Ares\Validation\Rule\EmailRule
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $emailRule = new EmailRule();

        $this->assertInstanceOf($fqcn, $emailRule);
    }

    /**
     * @covers ::getSupportedTypes
     *
     * @testWith ["string"]
     *
     * @param string $type Supported type.
     * @return void
     */
    public function testGetSupportedTypes(string $type): void
    {
        $emailRule = new EmailRule();

        $this->assertContains($type, $emailRule->getSupportedTypes());
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
        $emailRule = new EmailRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $emailRule->performValidation($args, $data, $context);
    }

    /**
     * @covers ::performValidation
     *
     * @testWith [true,  "john.doe@example.com", true]
     *           [true,  42,                     false]
     *           [true,  "foo.bar",              false]
     *           [false, "foo.bar",              true]
     *           [false, "john.doe@example.com", true]
     *           [false, 42,                     true]
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
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(EmailRule::ID, $args))
        );

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(EmailRule::ID, EmailRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $emailRule = new EmailRule();

        $this->assertSame($expectedRetVal, $emailRule->performValidation($args, $data, $context));
    }

    /**
     * @covers ::performValidation
     *
     * @return void
     */
    public function testValidateHandlesCustomMessage(): void
    {
        $args = true;
        $data = 'foo';
        $customMessage = 'The provided value must be a valid email address';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(EmailRule::ID, $args, $customMessage))
        );

        $context->expects($this->once())
            ->method('addError')
            ->with(EmailRule::ID, $customMessage);

        $emailRule = new EmailRule();

        $this->assertFalse($emailRule->performValidation($args, $data, $context));
    }
}

