<?php

declare(strict_types=1);

/**
 * UrlRuleTest.php
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
use Ares\Validation\Rule\UrlRule;
use PHPUnit\Framework\TestCase;

/**
 * Class UrlRuleTest
 *
 * @covers \Ares\Validation\Rule\AbstractRule
 * @coversDefaultClass \Ares\Validation\Rule\UrlRule
 */
class UrlRuleTest extends TestCase
{
    /**
     * @covers \Ares\Validation\Rule\UrlRule
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $urlRule = new UrlRule();

        $this->assertInstanceOf($fqcn, $urlRule);
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
        $urlRule = new UrlRule();

        $this->assertContains($type, $urlRule->getSupportedTypes());
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
        $urlRule = new UrlRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $urlRule->performValidation($args, $data, $context);
    }

    /**
     * @covers ::performValidation
     *
     * @testWith [true,  "https://example.com", true]
     *           [true,  42,                    false]
     *           [true,  "foo.bar",             false]
     *           [false, "foo.bar",             true]
     *           [false, "https://example.com", true]
     *           [false, 42,                    true]
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
            ->onlyMethods(['addError'])
            ->getMock();

        $context->enter('', (new Schema())->setRule(new Rule(UrlRule::ID, $args)));

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(UrlRule::ID, UrlRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $urlRule = new UrlRule();

        $this->assertSame($expectedRetVal, $urlRule->performValidation($args, $data, $context));
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
        $customMessage = 'Please enter a valid URL';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->onlyMethods(['addError'])
            ->getMock();

        $context->enter('', (new Schema())->setRule(new Rule(UrlRule::ID, $args, $customMessage)));

        $context->expects($this->once())
            ->method('addError')
            ->with(UrlRule::ID, $customMessage);

        $urlRule = new UrlRule();

        $this->assertFalse($urlRule->performValidation($args, $data, $context));
    }
}

