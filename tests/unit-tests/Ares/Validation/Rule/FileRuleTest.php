<?php

declare(strict_types=1);

/**
 * FileRuleTest.php
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
use Ares\Validation\Rule\FileRule;
use Ares\Validation\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class FileRuleTest
 *
 * @covers \Ares\Validation\Rule\AbstractRule
 * @coversDefaultClass \Ares\Validation\Rule\FileRule
 */
class FileRuleTest extends TestCase
{
    /**
     * @covers \Ares\Validation\Rule\FileRule
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $fileRule = new FileRule();

        $this->assertInstanceOf($fqcn, $fileRule);
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
        $fileRule = new FileRule();

        $this->assertContains($type, $fileRule->getSupportedTypes());
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
        $fileRule = new FileRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $fileRule->performValidation($args, $data, $context);
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
        $fileRule = new FileRule();

        $this->expectException(InapplicableValidationRuleException::class);
        $this->expectExceptionMessage('This rule applies to <string> types only');

        $fileRule->performValidation(true, $data, $context);
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
            ->onlyMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(FileRule::ID, $args))
        );

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(FileRule::ID, FileRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $fileRule = new FileRule();

        $this->assertSame($expectedRetVal, $fileRule->performValidation($args, $data, $context));
    }

    /**
     * @return array
     */
    public static function getValidateSamples(): array
    {
        return [
            'valid file #1' => [
                true,
                __FILE__,
                true,
            ],
            'valid file #2' => [
                false,
                __FILE__,
                true,
            ],
            'invalid file #1' => [
                true,
                __FILE__ . uniqid(),
                false,
            ],
            'invalid file #2' => [
                true,
                __DIR__,
                false,
            ],
            'invalid file #3' => [
                false,
                __FILE__ . uniqid(),
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
        $customMessage = 'The provided value must be a valid file';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->onlyMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(FileRule::ID, $args, $customMessage))
        );

        $context->expects($this->once())
            ->method('addError')
            ->with(FileRule::ID, $customMessage);

        $fileRule = new FileRule();

        $this->assertFalse($fileRule->performValidation($args, $data, $context));
    }
}
