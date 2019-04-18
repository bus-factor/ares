<?php

declare(strict_types=1);

/**
 * MaxLengthRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\MaxLengthRule;
use Ares\Rule\TypeRule;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use PHPUnit\Framework\TestCase;

/**
 * Class MaxLengthRuleTest
 *
 * @coversDefaultClass \Ares\Rule\MaxLengthRule
 */
class MaxLengthRuleTest extends TestCase
{
    /**
     * @testWith ["Ares\\Rule\\RuleInterface"]
     *           ["Ares\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $maxLengthRule = new MaxLengthRule();

        $this->assertInstanceOf($fqcn, $maxLengthRule);
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
        $maxLengthRule = new MaxLengthRule();

        $this->assertContains($type, $maxLengthRule->getSupportedTypes());
    }

    /**
     * @covers ::performValidation
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
        $maxLengthRule = new MaxLengthRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $maxLengthRule->performValidation($args, $data, $context);
    }

    /**
     * @covers ::performValidation
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

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(MaxLengthRule::ID, $args))
        );

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(MaxLengthRule::ID, MaxLengthRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $maxLengthRule = new MaxLengthRule();

        $this->assertSame($expectedRetVal, $maxLengthRule->performValidation($args, $data, $context));
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
                5,
                'foo',
                true,
            ],
            'invalid string #1' => [
                5,
                'foobar',
                false,
            ],
        ];
    }

    /**
     * @covers ::performValidation
     *
     * @return void
     */
    public function testValidateHandlesCustomMessage(): void
    {
        $args = 2;
        $data = 'foo';
        $customMessage = 'The provided value must not be longer than 2 chars';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(MaxLengthRule::ID, $args, $customMessage))
        );

        $context->expects($this->once())
            ->method('addError')
            ->with(MaxLengthRule::ID, $customMessage);

        $maxLengthRule = new MaxLengthRule();

        $this->assertFalse($maxLengthRule->performValidation($args, $data, $context));
    }
}

