<?php

declare(strict_types=1);

/**
 * RegexRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InapplicableValidationRuleException;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\RegexRule;
use Ares\Rule\TypeRule;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use PHPUnit\Framework\TestCase;

/**
 * Class RegexRuleTest
 *
 * @coversDefaultClass \Ares\Rule\RegexRule
 */
class RegexRuleTest extends TestCase
{
    /**
     * @covers ::validate
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

        $regexRule->validate($args, $data, $context);
    }

    /**
     * @covers ::validate
     *
     * @testWith ["integer"]
     *           ["float"]
     *           ["map"]
     *           ["boolean"]
     *
     * @param mixed $args Validation rule configuration.
     * @return void
     */
    public function testValidateToHandleInapplicableValidationRule(string $type): void
    {
        $context = new Context($data, new ErrorMessageRenderer());
        $context->enter('', (new Schema())->setRule(new Rule(TypeRule::ID, $type)));

        $regexRule = new RegexRule();

        $this->expectException(InapplicableValidationRuleException::class);
        $this->expectExceptionMessage('This rule applies to <string> types only');

        $regexRule->validate(null, null, $context);
    }

    /**
     * @covers ::validate
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

        $this->assertSame($expectedRetVal, $regexRule->validate($args, $data, $context));
    }

    /**
     * @covers ::validate
     *
     * @return void
     */
    public function testValidateHandlesCustomMessage(): void
    {
        $args = '/^foo/';
        $data = 'bar';
        $customMessage = 'The value must start with "foo"';

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

        $this->assertFalse($regexRule->validate($args, $data, $context));
    }
}

