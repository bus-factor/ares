<?php

declare(strict_types=1);

/**
 * MinLengthRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\MinLengthRule;
use PHPUnit\Framework\TestCase;

/**
 * Class MinLengthRuleTest
 *
 * @coversDefaultClass \Ares\Rule\MinLengthRule
 */
class MinLengthRuleTest extends TestCase
{
    /**
     * @covers ::validate
     *
     * @testWith [17.2]
     *           [null]
     *           ["foo"]
     *           [true]
     *           [false]
     *           [-1]
     *           [-42]
     *
     * @param mixed $args Validation rule configuration.
     * @return void
     */
    public function testValidateToHandleInvalidValidationRuleArgs($args): void
    {
        $data = 'foo';
        $context = new Context($data, new ErrorMessageRenderer());
        $minLengthRule = new MinLengthRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $minLengthRule->validate($args, $data, $context);
    }

    /**
     * @covers ::validate
     *
     * @dataProvider getValidateSamples
     *
     * @param mixed $args           Validation rule configuration.
     * @param mixed $data           Validated data.
     * @param bool  $expectedRetVal Expected validation return value.
     * @return void
     */
    public function testValidate($args, $data, bool $expectedRetVal): void
    {
        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(MinLengthRule::ID, MinLengthRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $minLengthRule = new MinLengthRule();

        $this->assertSame($expectedRetVal, $minLengthRule->validate($args, $data, $context));
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
    {
        return [
            'non-string value #1' => [
                10,
                42,
                true,
            ],
            'non-string value #2' => [
                10,
                true,
                true,
            ],
            'non-string value #3' => [
                10,
                false,
                true,
            ],
            'non-string value #4' => [
                10,
                13.37,
                true,
            ],
            'non-string value #5' => [
                10,
                [],
                true,
            ],
            'valid string #1' => [
                3,
                'foo',
                true,
            ],
            'valid string #2' => [
                3,
                'foobar',
                true,
            ],
            'invalid string #1' => [
                5,
                'foo',
                false,
            ],
        ];
    }
}

