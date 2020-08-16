<?php

declare(strict_types=1);

/**
 * AllowedRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace UnitTest\Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Validation\Context;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Rule\AllowedRule;
use PHPUnit\Framework\TestCase;

/**
 * Class AllowedRuleTest
 *
 * @coversDefaultClass \Ares\Validation\Rule\AllowedRule
 */
class AllowedRuleTest extends TestCase
{
    /**
     * @covers \Ares\Validation\Rule\AllowedRule
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $allowedRule = new AllowedRule();

        $this->assertInstanceOf($fqcn, $allowedRule);
    }

    /**
     * @covers ::getSupportedTypes
     *
     * @testWith ["boolean"]
     *           ["float"]
     *           ["integer"]
     *           ["numeric"]
     *           ["string"]
     *
     * @param string $type Supported type.
     * @return void
     */
    public function testGetSupportedTypes(string $type): void
    {
        $allowedRule = new AllowedRule();

        $this->assertContains($type, $allowedRule->getSupportedTypes());
    }

    /**
     * @covers ::performValidation
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

        $allowedRule->performValidation($args, $data, $context);
    }

    /**
     * @covers ::performValidation
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

        $this->assertSame($expectedRetVal, $allowedRule->performValidation($args, $data, $context));
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
     * @covers ::performValidation
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

        $this->assertFalse($allowedRule->performValidation([], 'some-value', $context));
    }
}

