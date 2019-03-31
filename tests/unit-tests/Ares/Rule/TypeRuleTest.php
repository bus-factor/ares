<?php

declare(strict_types=1);

/**
 * TypeRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class TypeRuleTest
 *
 * @coversDefaultClass \Ares\Rule\TypeRule
 */
class TypeRuleTest extends TestCase
{
    /**
     * @covers ::validate
     *
     * @testWith [1]
     *           [17.2]
     *           [null]
     *           ["foo"]
     *           [[]]
     *           [{}]
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
        $typeRule = new TypeRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $typeRule->validate($args, $data, $context);
    }

    /**
     * @covers ::validate
     *
     * @dataProvider getValidateSamples
     *
     * @param string $args           Validation rule configuration.
     * @param mixed  $data           Validated data.
     * @param bool   $expectedRetVal Expected validation return value.
     * @return void
     */
    public function testValidate(string $args, $data, bool $expectedRetVal): void
    {
        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(TypeRule::ID, TypeRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $typeRule = new TypeRule();

        $this->assertSame($expectedRetVal, $typeRule->validate($args, $data, $context));
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
    {
        return [
            'array + map'              => ['map',     [],              true],
            'boolean + boolean(true)'  => ['boolean', true,            true],
            'boolean + boolean(false)' => ['boolean', false,           true],
            'double + float'           => ['float',   13.37,           true],
            'integer + integer'        => ['integer', 1337,            true],
            'string + string'          => ['string',  'John Doe',      true],
            'null + map'               => ['map',     null,            true],
            'null + boolean(true)'     => ['boolean', null,            true],
            'null + float'             => ['float',   null,            true],
            'null + integer'           => ['integer', null,            true],
            'null + string'            => ['string',  null,            true],
            'float + map'              => ['map',     1.337,           false],
            'integer + boolean'        => ['boolean', 42,              false],
            'array + float'            => ['float',   [],              false],
            'string + integer'         => ['integer', 'John Doe',      false],
            'boolean + string'         => ['string',  true,            false],
            'object + string'          => ['string',  new \stdClass(), false],
            'array + list'             => ['list',    [],              true],
            'integer + list'           => ['list',    1337,            false],
        ];
    }
}

