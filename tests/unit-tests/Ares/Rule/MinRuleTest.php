<?php

declare(strict_types=1);

/**
 * MinRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InapplicableValidationRuleException;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\MinRule;
use Ares\Rule\TypeRule;
use Ares\Schema\Type;
use PHPUnit\Framework\TestCase;

/**
 * Class MinRuleTest
 *
 * @coversDefaultClass \Ares\Rule\MinRule
 */
class MinRuleTest extends TestCase
{
    /**
     * @covers ::validate
     *
     * @testWith [null]
     *           ["foo"]
     *           [true]
     *           [false]
     *           [[]]
     *
     * @param mixed $args Validation rule configuration.
     * @return void
     */
    public function testValidateToHandleInapplicabeValidationRule($type): void
    {
        $context = new Context($data, new ErrorMessageRenderer());
        $context->enter('', [TypeRule::ID => $type]);

        $minRule = new MinRule();

        $this->expectException(InapplicableValidationRuleException::class);
        $this->expectExceptionMessage('This rule applies to <float> and <integer> types only');

        $minRule->validate(null, null, $context);
    }

    /**
     * @covers ::validate
     *
     * @testWith [null]
     *           ["foo"]
     *           [true]
     *           [false]
     *           [[]]
     *
     * @param mixed $args Validation rule configuration.
     * @return void
     */
    public function testValidateToHandleInvalidValidationRuleArgs($args): void
    {
        $data = 'foo';

        $context = new Context($data, new ErrorMessageRenderer());
        $context->enter('', [TypeRule::ID => Type::INTEGER]);

        $minRule = new MinRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $minRule->validate($args, $data, $context);
    }

    /**
     * @covers ::validate
     *
     * @dataProvider getValidateSamples
     *
     * @param string        $type           Schema value type.
     * @param integer|float $args           Validation rule configuration.
     * @param mixed         $data           Validated data.
     * @param bool          $expectedRetVal Expected validation return value.
     * @return void
     */
    public function testValidate($type, $args, $data, bool $expectedRetVal): void
    {
        $errorMessageRenderer = new ErrorMessageRenderer();

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, $errorMessageRenderer])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter('', [TypeRule::ID => $type]);

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(MinRule::ID, $errorMessageRenderer->render($context, MinRule::ID, MinRule::ERROR_MESSAGE, ['value' => $args]));
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $minRule = new MinRule();

        $this->assertSame($expectedRetVal, $minRule->validate($args, $data, $context));
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
    {
        return [
            'integer value too small' => [Type::INTEGER, 12, 11, false],
            'integer value is min value' => [Type::INTEGER, 12, 12, true],
            'integer value is greater min value' => [Type::INTEGER, 12, 13, true],
            'float value too small' => [Type::FLOAT, 12.2, 11.1, false],
            'float value is min value' => [Type::FLOAT, 12.2, 12.2, true],
            'float value is greater min value' => [Type::FLOAT, 12.2, 13.3, true],
        ];
    }
}

