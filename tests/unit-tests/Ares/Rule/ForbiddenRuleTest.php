<?php

declare(strict_types=1);

/**
 * ForbiddenRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace UnitTest\Ares\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Context;
use Ares\Rule\ForbiddenRule;
use PHPUnit\Framework\TestCase;

/**
 * Class ForbiddenRuleTest
 *
 * @coversDefaultClass \Ares\Rule\ForbiddenRule
 */
class ForbiddenRuleTest extends TestCase
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
        $context = new Context();
        $forbiddenRule = new ForbiddenRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $forbiddenRule->validate($args, $data, $context);
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
            ->setMethods(['addError'])
            ->getMock();

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(ForbiddenRule::ID, ForbiddenRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $forbiddenRule = new ForbiddenRule();

        $this->assertSame($expectedRetVal, $forbiddenRule->validate($args, $data, $context));
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
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
}

