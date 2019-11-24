<?php

declare(strict_types=1);

/**
 * UuidRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

namespace UnitTest\Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Validation\Context;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Rule\UuidRule;
use PHPUnit\Framework\TestCase;

/**
 * Class UuidRuleTest
 *
 * @coversDefaultClass \Ares\Validation\Rule\UuidRule
 */
class UuidRuleTest extends TestCase
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
        $uuidRule = new UuidRule();

        $this->assertInstanceOf($fqcn, $uuidRule);
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
        $uuidRule = new UuidRule();

        $this->assertContains($type, $uuidRule->getSupportedTypes());
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
        $uuidRule = new UuidRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $uuidRule->performValidation($args, $data, $context);
    }

    /**
     * @covers ::performValidation
     *
     * @testWith [true,  "609de7b6-0ef5-11ea-8d71-362b9e155667", true]
     *           [true,  42,                                     false]
     *           [true,  "foo.bar",                              false]
     *           [false, "foo.bar",                              true]
     *           [false, "609de7b6-0ef5-11ea-8d71-362b9e155667", true]
     *           [false, 42,                                     true]
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

        $context->enter('', (new Schema())->setRule(new Rule(UuidRule::ID, $args)));

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(UuidRule::ID, UuidRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $uuidRule = new UuidRule();

        $this->assertSame($expectedRetVal, $uuidRule->performValidation($args, $data, $context));
    }

    /**
     * @covers ::performValidation
     *
     * @return void
     */
    public function testValidateHandlesCustomMessage(): void
    {
        $args = true;
        $data = 'asdf';
        $customMessage = 'Please enter a valid UUID';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter('', (new Schema())->setRule(new Rule(UuidRule::ID, $args, $customMessage)));

        $context->expects($this->once())
            ->method('addError')
            ->with(UuidRule::ID, $customMessage);

        $uuidRule = new UuidRule();

        $this->assertFalse($uuidRule->performValidation($args, $data, $context));
    }
}

