<?php

declare(strict_types=1);

/**
 * EmailRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\EmailRule;
use PHPUnit\Framework\TestCase;

/**
 * Class EmailRuleTest
 *
 * @coversDefaultClass \Ares\Rule\EmailRule
 */
class EmailRuleTest extends TestCase
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
        $emailRule = new EmailRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $emailRule->validate($args, $data, $context);
    }

    /**
     * @covers ::validate
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

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(EmailRule::ID, EmailRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $emailRule = new EmailRule();

        $this->assertSame($expectedRetVal, $emailRule->validate($args, $data, $context));
    }
}

