<?php

declare(strict_types=1);

/**
 * DirectoryRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-17
 */

namespace UnitTest\Ares\Validation\Rule;

use Ares\Exception\InapplicableValidationRuleException;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use Ares\Validation\Context;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Rule\DirectoryRule;
use Ares\Validation\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class DirectoryRuleTest
 *
 * @covers \Ares\Validation\Rule\AbstractRule
 * @coversDefaultClass \Ares\Validation\Rule\DirectoryRule
 */
class DirectoryRuleTest extends TestCase
{
    /**
     * @covers \Ares\Validation\Rule\DirectoryRule
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $directoryRule = new DirectoryRule();

        $this->assertInstanceOf($fqcn, $directoryRule);
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
        $directoryRule = new DirectoryRule();

        $this->assertContains($type, $directoryRule->getSupportedTypes());
    }

    /**
     * @covers ::performValidation
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
        $directoryRule = new DirectoryRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $directoryRule->performValidation($args, $data, $context);
    }

    /**
     * @covers ::performValidation
     *
     * @testWith [1]
     *           [17.2]
     *           [null]
     *           [true]
     *           [false]
     *           [[]]
     *           [{}]
     *
     * @param mixed $data Input data.
     * @return void
     */
    public function testValidateToHandleInvalidData($data): void
    {
        $context = new Context($data, new ErrorMessageRenderer());
        $directoryRule = new DirectoryRule();

        $this->expectException(InapplicableValidationRuleException::class);
        $this->expectExceptionMessage('This rule applies to <string> types only');

        $directoryRule->performValidation(true, $data, $context);
    }

    /**
     * @covers ::performValidation
     *
     * @dataProvider getValidateSamples
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
                ->setRule(new Rule(DirectoryRule::ID, $args))
        );

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(DirectoryRule::ID, DirectoryRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $directoryRule = new DirectoryRule();

        $this->assertSame($expectedRetVal, $directoryRule->performValidation($args, $data, $context));
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
    {
        return [
            'valid directory #1' => [
                true,
                __DIR__,
                true,
            ],
            'valid directory #2' => [
                false,
                __DIR__,
                true,
            ],
            'invalid directory #1' => [
                true,
                __DIR__ . uniqid(),
                false,
            ],
            'invalid directory #2' => [
                true,
                __FILE__,
                false,
            ],
            'invalid directory #3' => [
                false,
                __DIR__ . uniqid(),
                true,
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
        $args = true;
        $data = 'foo';
        $customMessage = 'The provided value must be a valid directory';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(DirectoryRule::ID, $args, $customMessage))
        );

        $context->expects($this->once())
            ->method('addError')
            ->with(DirectoryRule::ID, $customMessage);

        $directoryRule = new DirectoryRule();

        $this->assertFalse($directoryRule->performValidation($args, $data, $context));
    }
}

