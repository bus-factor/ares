<?php

declare(strict_types=1);

/**
 * LengthRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-19
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\LengthRule;
use Ares\Rule\TypeRule;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use PHPUnit\Framework\TestCase;

/**
 * Class LengthRuleTest
 *
 * @coversDefaultClass \Ares\Rule\LengthRule
 */
class LengthRuleTest extends TestCase
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
        $lengthRule = new LengthRule();

        $this->assertInstanceOf($fqcn, $lengthRule);
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
        $lengthRule = new LengthRule();

        $this->assertContains($type, $lengthRule->getSupportedTypes());
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
        $lengthRule = new LengthRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $lengthRule->performValidation($args, $data, $context);
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
                ->setRule(new Rule(LengthRule::ID, $args))
        );

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(LengthRule::ID, LengthRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $lengthRule = new LengthRule();

        $this->assertSame($expectedRetVal, $lengthRule->performValidation($args, $data, $context));
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
            'invalid string #1' => [
                3,
                'fo',
                false,
            ],
            'invalid string #2' => [
                3,
                'fooo',
                false,
            ],
            'valid list #1' => [
                3,
                [1, 2, 3],
                true,
            ],
            'invalid list #1' => [
                3,
                [1, 2],
                false,
            ],
            'invalid list #2' => [
                3,
                [1, 2, 3, 4],
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
        $customMessage = 'The provided value must be exactly 2 chars long';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(LengthRule::ID, $args, $customMessage))
        );

        $context->expects($this->once())
            ->method('addError')
            ->with(LengthRule::ID, $customMessage);

        $lengthRule = new LengthRule();

        $this->assertFalse($lengthRule->performValidation($args, $data, $context));
    }
}

