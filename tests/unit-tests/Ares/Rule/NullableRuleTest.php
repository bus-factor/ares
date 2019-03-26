<?php

declare(strict_types=1);

/**
 * NullableRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\NullableRule;
use PHPUnit\Framework\TestCase;

/**
 * Class NullableRuleTest
 *
 * @coversDefaultClass \Ares\Rule\NullableRule
 */
class NullableRuleTest extends TestCase
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
        $nullableRule = new NullableRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $nullableRule->validate($args, $data, $context);
    }

    /**
     * @covers ::validate
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

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(NullableRule::ID, NullableRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $nullableRule = new NullableRule();

        $this->assertSame($expectedRetVal, $nullableRule->validate($args, $data, $context));
    }
}

