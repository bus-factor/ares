<?php

declare(strict_types=1);

/**
 * RegexRuleTest.php
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
use Ares\Validation\Rule\RegexRule;
use Ares\Validation\Rule\TypeRule;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class RegexRuleTest
 *
 * @covers \Ares\Validation\Rule\AbstractRule
 * @coversDefaultClass \Ares\Validation\Rule\RegexRule
 */
class RegexRuleTest extends TestCase
{
    /**
     * @covers \Ares\Validation\Rule\RegexRule
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $regexRule = new RegexRule();

        $this->assertInstanceOf($fqcn, $regexRule);
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
        $regexRule = new RegexRule();

        $this->assertContains($type, $regexRule->getSupportedTypes());
    }

    /**
     * @covers ::performValidation
     *
     * @testWith [1]
     *           [17.2]
     *           [null]
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
        $context->enter('', (new Schema())->setRule(new Rule(TypeRule::ID, Type::STRING)));

        $regexRule = new RegexRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $regexRule->performValidation($args, $data, $context);
    }

    /**
     * @covers ::performValidation
     *
     * @testWith ["/^foo$/", "foo", true]
     *           ["/^foo$/", "bar", false]
     *
     * @param string $args           Validation rule configuration.
     * @param mixed  $data           Validated data.
     * @param bool   $expectedRetVal Expected validation return value.
     * @return void
     */
    public function testValidate(string $args, $data, bool $expectedRetVal): void
    {
        /** @var Context&MockObject $context */
        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(RegexRule::ID, $args))
        );

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(RegexRule::ID, RegexRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $regexRule = new RegexRule();

        $this->assertSame($expectedRetVal, $regexRule->performValidation($args, $data, $context));
    }

    /**
     * @covers ::performValidation
     *
     * @return void
     */
    public function testValidateHandlesCustomMessage(): void
    {
        $args = '/^foo/';
        $data = 'bar';
        $customMessage = 'The value must start with "foo"';

        /** @var Context&MockObject $context */
        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(RegexRule::ID, $args, $customMessage))
        );

        $context->expects($this->once())
            ->method('addError')
            ->with(RegexRule::ID, $customMessage);

        $regexRule = new RegexRule();

        $this->assertFalse($regexRule->performValidation($args, $data, $context));
    }

    /**
     * @covers ::performValidation
     *
     * @return void
     */
    public function testCorruptRegexPatternHandling(): void
    {
        $args = '/^foo';
        $data = 'bar';

        /** @var Context&MockObject $context */
        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
        );

        $regexRule = new RegexRule();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Regex pattern possibly corrupt: ' . $args);

        $regexRule->performValidation($args, $data, $context);
    }
}

