<?php

declare(strict_types=1);

/**
 * DateTimeRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-22
 */

namespace UnitTest\Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use Ares\Validation\Context;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Rule\DateTimeRule;
use Ares\Validation\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeRuleTest
 *
 * @coversDefaultClass \Ares\Validation\Rule\DateTimeRule
 */
class DateTimeRuleTest extends TestCase
{
    /**
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $dateTimeRule = new DateTimeRule();

        $this->assertInstanceOf($fqcn, $dateTimeRule);
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
        $dateTimeRule = new DateTimeRule();

        $this->assertContains($type, $dateTimeRule->getSupportedTypes());
    }

    /**
     * @covers ::performValidation
     *
     * @testWith [1]
     *           [17.2]
     *           [null]
     *           [[]]
     *
     * @param mixed $args Validation rule configuration.
     * @return void
     */
    public function testValidateToHandleInvalidValidationRuleArgs($args): void
    {
        $data = 'foo';
        $context = new Context($data, new ErrorMessageRenderer());
        $dateTimeRule = new DateTimeRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $dateTimeRule->performValidation($args, $data, $context);
    }

    /**
     * @covers ::performValidation
     * @covers ::validateDataProcessability
     * @covers ::validateDateTimeFormat
     *
     * @dataProvider getValidateSamples
     *
     * @param bool|array  $args                 Validation rule configuration.
     * @param mixed       $data                 Validated data.
     * @param bool        $expectedRetVal       Expected validation return value.
     * @param string|null $expectedErrorMessage Expected validation error message.
     * @return void
     */
    public function testValidate($args, $data, bool $expectedRetVal, ?string $expectedErrorMessage = null): void
    {
        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter(
            '',
            (new Schema())
                ->setRule(new Rule(TypeRule::ID, Type::STRING))
                ->setRule(new Rule(DateTimeRule::ID, $args))
        );
        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(DateTimeRule::ID, $expectedErrorMessage);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $dateTimeRule = new DateTimeRule();

        $this->assertSame($expectedRetVal, $dateTimeRule->performValidation($args, $data, $context));
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
    {
        return [
            'args = false' => [
                false,
                '',
                true
            ],
            'args = true | invalid data #1'  => [
                true,
                '',
                false,
                DateTimeRule::ERROR_MESSAGE,
            ],
            'args = true | invalid data #2'  => [
                true,
                1337,
                false,
                DateTimeRule::ERROR_MESSAGE,
            ],
            'args = true | invalid data #3'  => [
                true,
                13.37,
                false,
                DateTimeRule::ERROR_MESSAGE,
            ],
            'args = true | invalid data #4'  => [
                true,
                true,
                false,
                DateTimeRule::ERROR_MESSAGE,
            ],
            'args = true | invalid data #5'  => [
                true,
                'no date',
                false,
                DateTimeRule::ERROR_MESSAGE,
            ],
            'args = true | valid data #1'  => [
                true,
                '2019-03-21',
                true,
            ],
            'args = true | valid data #2'  => [
                true,
                '21.3.19',
                true,
            ],
            'args = "d.m.Y" | invalid data #1' => [
                'd.m.Y',
                '2019-0883-22',
                false,
                DateTimeRule::ERROR_MESSAGE,
            ],
            'args = "d.m.Y" | invalid data #2' => [
                'd.m.Y',
                '2019-03-22',
                false,
                DateTimeRule::ERROR_MESSAGE,
            ],
            'args = "d.m.Y" | valid data' => [
                'd.m.Y',
                '22.03.2019',
                true
            ],
        ];
    }
}

