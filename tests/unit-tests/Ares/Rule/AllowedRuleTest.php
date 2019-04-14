<?php

declare(strict_types=1);

/**
 * AllowedRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\AllowedRule;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use PHPUnit\Framework\TestCase;

/**
 * Class AllowedRuleTest
 *
 * @coversDefaultClass \Ares\Rule\AllowedRule
 */
class AllowedRuleTest extends TestCase
{
    /**
     * @covers ::validate
     *
     * @testWith [1]
     *           [17.2]
     *           [null]
     *           ["foo"]
     *           [true]
     *           [false]
     *
     * @param mixed $args Validation rule configuration.
     * @return void
     */
    public function testValidateToHandleInvalidValidationRuleArgs($args): void
    {
        $data = 'foo';
        $context = new Context($data, new ErrorMessageRenderer());
        $allowedRule = new AllowedRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $allowedRule->validate($args, $data, $context);
    }

    /**
     * @covers ::validate
     *
     * @dataProvider getValidateSamples
     *
     * @param array $args           Validation rule configuration.
     * @param mixed $data           Validated data.
     * @param bool  $expectedRetVal Expected validation return value.
     * @return void
     */
    public function testValidate(array $args, $data, bool $expectedRetVal): void
    {
        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter('', (new Schema())->setRule(new Rule(AllowedRule::ID, $args)));

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(AllowedRule::ID, AllowedRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $allowedRule = new AllowedRule();

        $this->assertSame($expectedRetVal, $allowedRule->validate($args, $data, $context));
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
    {
        return [
            'allowed value #1' => [
                ['foo', 'bar'],
                'foo',
                true,
            ],
            'allowed value #2' => [
                ['foo', 'bar'],
                'bar',
                true,
            ],
            'mixed allowed values #1' => [
                [1, 'foo'],
                1,
                true,
            ],
            'mixed allowed values #2' => [
                [1, 'foo'],
                'foo',
                true,
            ],
            'mixed allowed values #3' => [
                [['foo'], 'bar'],
                ['foo'],
                true,
            ],
            'forbidden value #1' => [
                ['foo', 'bar'],
                'fizz',
                false,
            ],
            'forbidden value #2' => [
                ['1', 'bar'],
                1,
                false,
            ],
            'forbidden value #3' => [
                ['1.0', 'bar'],
                1,
                false,
            ],
            'forbidden value #4' => [
                ['1.0', 'bar'],
                1.0,
                false,
            ],
            'forbidden value #5' => [
                [['foo'], 'bar'],
                ['fizz'],
                false,
            ],
        ];
    }

    /**
     * @covers ::validate
     *
     * @return void
     */
    public function testValidateHandlesCustomMessage(): void
    {
        $customMessage = 'This value is not allowed at all';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter('', (new Schema())->setRule(new Rule(AllowedRule::ID, [], $customMessage)));

        $context->expects($this->once())
            ->method('addError')
            ->with(AllowedRule::ID, $customMessage);

        $allowedRule = new AllowedRule();

        $this->assertFalse($allowedRule->validate([], 'some-value', $context));
    }
}

