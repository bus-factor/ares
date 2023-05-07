<?php

declare(strict_types=1);

/**
 * ForbiddenRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace UnitTest\Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use Ares\Validation\Context;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Rule\ForbiddenRule;
use Ares\Validation\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class ForbiddenRuleTest
 *
 * @covers \Ares\Validation\Rule\AbstractRule
 * @coversDefaultClass \Ares\Validation\Rule\ForbiddenRule
 */
class ForbiddenRuleTest extends TestCase
{
    /**
     * @covers \Ares\Validation\Rule\ForbiddenRule
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $forbiddenRule = new ForbiddenRule();

        $this->assertInstanceOf($fqcn, $forbiddenRule);
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
        $forbiddenRule = new ForbiddenRule();

        $this->assertContains($type, $forbiddenRule->getSupportedTypes());
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
        $forbiddenRule = new ForbiddenRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $forbiddenRule->performValidation($args, $data, $context);
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
            ->onlyMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(ForbiddenRule::ID, $args))
        );

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(ForbiddenRule::ID, ForbiddenRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $forbiddenRule = new ForbiddenRule();

        $this->assertSame($expectedRetVal, $forbiddenRule->performValidation($args, $data, $context));
    }

    /**
     * @return array
     */
    public static function getValidateSamples(): array
    {
        return [
            'forbidden value #1'        => [['foo', 'bar'],   'foo',    false],
            'forbidden value #2'        => [['foo', 'bar'],   'bar',    false],
            'mixed forbidden values #1' => [[1, 'foo'],       1,        false],
            'mixed forbidden values #2' => [[1, 'foo'],       'foo',    false],
            'mixed forbidden values #3' => [[['foo'], 'bar'], ['foo'],  false],
            'allowed value #1'          => [['foo', 'bar'],   'fizz',   true],
            'allowed value #2'          => [['1', 'bar'],     1,        true],
            'allowed value #3'          => [['1.0', 'bar'],   1,        true],
            'allowed value #4'          => [['1.0', 'bar'],   1.0,      true],
            'allowed value #5'          => [[['foo'], 'bar'], ['fizz'], true],
        ];
    }

    /**
     * @covers ::performValidation
     *
     * @return void
     */
    public function testValidateHandlesCustomMessage(): void
    {
        $args = ['foo', 'bar'];
        $data = 'foo';
        $customMessage = 'Only "foo" or "bar" are forbidden';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->onlyMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(ForbiddenRule::ID, $args, $customMessage))
        );

        $context->expects($this->once())
            ->method('addError')
            ->with(ForbiddenRule::ID, $customMessage);

        $forbiddenRule = new ForbiddenRule();

        $this->assertFalse($forbiddenRule->performValidation($args, $data, $context));
    }
}
