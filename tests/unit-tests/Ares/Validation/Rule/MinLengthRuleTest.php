<?php

declare(strict_types=1);

/**
 * MinLengthRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

namespace UnitTest\Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use Ares\Validation\Context;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Rule\MinLengthRule;
use Ares\Validation\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class MinLengthRuleTest
 *
 * @coversDefaultClass \Ares\Validation\Rule\MinLengthRule
 */
class MinLengthRuleTest extends TestCase
{
    /**
     * @covers \Ares\Validation\Rule\MinLengthRule
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $minLengthRule = new MinLengthRule();

        $this->assertInstanceOf($fqcn, $minLengthRule);
    }

    /**
     * @covers ::getSupportedTypes
     *
     * @testWith ["list"]
     *           ["string"]
     *
     * @param string $type Supported type.
     * @return void
     */
    public function testGetSupportedTypes(string $type): void
    {
        $minLengthRule = new MinLengthRule();

        $this->assertContains($type, $minLengthRule->getSupportedTypes());
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
        $minLengthRule = new MinLengthRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $minLengthRule->performValidation($args, $data, $context);
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
                ->setRule(new Rule(MinLengthRule::ID, $args))
        );

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(MinLengthRule::ID, MinLengthRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $minLengthRule = new MinLengthRule();

        $this->assertSame($expectedRetVal, $minLengthRule->performValidation($args, $data, $context));
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
    {
        return [
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
            'valid list #1' => [
                3,
                [1, 2, 3],
                true,
            ],
            'invalid list #1' => [
                4,
                [1, 2, 3],
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
        $args = 3;
        $data = 'fo';
        $customMessage = 'Must be at least {value} chars long';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(MinLengthRule::ID, $args, $customMessage))
        );

        $context->expects($this->once())
            ->method('addError')
            ->with(MinLengthRule::ID, $customMessage);

        $minLengthRule = new MinLengthRule();

        $this->assertFalse($minLengthRule->performValidation($args, $data, $context));
    }
}

