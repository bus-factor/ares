<?php

declare(strict_types=1);

/**
 * BlankableRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\BlankableRule;
use Ares\Rule\TypeRule;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use PHPUnit\Framework\TestCase;

/**
 * Class BlankableRuleTest
 *
 * @coversDefaultClass \Ares\Rule\BlankableRule
 */
class BlankableRuleTest extends TestCase
{
    /**
     * @covers ::validate
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
        $blankableRule = new BlankableRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $blankableRule->validate($args, $data, $context);
    }

    /**
     * @covers ::validate
     *
     * @testWith [true,  "",     true]
     *           [false, 42,     true]
     *           [false, 13.37,  true]
     *           [false, null,   true]
     *           [false, [],     true]
     *           [false, {},     true]
     *           [false, "",     false]
     *           [false, "  ",   false]
     *           [false, "\n ",  false]
     *           [false, "\n\t", false]
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
                ->setRule(new Rule(BlankableRule::ID, $args)
            )
        );

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(BlankableRule::ID, BlankableRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $blankableRule = new BlankableRule();

        $this->assertSame($expectedRetVal, $blankableRule->validate($args, $data, $context));
    }

    /**
     * @covers ::validate
     *
     * @return void
     */
    public function testValidateHandlesCustomMessage(): void
    {
        $args = false;
        $data = '';
        $customMessage = 'Please provide a non-blank value';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(BlankableRule::ID, $args, $customMessage)
            )
        );

        $context->expects($this->once())
            ->method('addError')
            ->with(BlankableRule::ID, $customMessage);

        $blankableRule = new BlankableRule();

        $this->assertFalse($blankableRule->validate($args, $data, $context));
    }
}

