<?php

declare(strict_types=1);

/**
 * TypeRuleTest.php
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
use Ares\Validation\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class TypeRuleTest
 *
 * @coversDefaultClass \Ares\Validation\Rule\TypeRule
 */
class TypeRuleTest extends TestCase
{
    /**
     * @covers \Ares\Validation\Rule\TypeRule
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *           ["Ares\\Validation\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $typeRule = new TypeRule();

        $this->assertInstanceOf($fqcn, $typeRule);
    }

    /**
     * @covers ::getSupportedTypes
     *
     * @testWith ["boolean"]
     *           ["float"]
     *           ["integer"]
     *           ["list"]
     *           ["map"]
     *           ["numeric"]
     *           ["string"]
     *           ["tuple"]
     *
     * @param string $type Supported type.
     * @return void
     */
    public function testGetSupportedTypes(string $type): void
    {
        $typeRule = new TypeRule();

        $this->assertContains($type, $typeRule->getSupportedTypes());
    }

    /**
     * @covers ::isApplicable
     *
     * @return void
     */
    public function testIsApplicable(): void
    {
        $data = [];
        $typeRule = new TypeRule();
        $context = new Context($data, new ErrorMessageRenderer());

        $this->assertTrue($typeRule->isApplicable($context));
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

        $typeRule->performValidation($args, $data, $context);
    }

    /**
     * @covers ::performValidation
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

        $context->enter('', (new Schema())->setRule(new Rule(TypeRule::ID, $args)));

        if ($expectedRetVal === false) {
            $context->expects($this->once())
                ->method('addError')
                ->with(TypeRule::ID, TypeRule::ERROR_MESSAGE);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $typeRule = new TypeRule();

        $this->assertSame($expectedRetVal, $typeRule->performValidation($args, $data, $context));
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
    {
        return [
            'array + map' => [
                'map',
                [],
                true,
            ],
            'boolean + boolean(true)' => [
                'boolean',
                true,
                true,
            ],
            'boolean + boolean(false)' => [
                'boolean',
                false,
                true,
            ],
            'double + float' => [
                'float',
                13.37,
                true,
            ],
            'integer + integer' => [
                'integer',
                1337,
                true,
            ],
            'string + string' => [
                'string',
                'John Doe',
                true,
            ],
            'null + map' => [
                'map',
                null,
                true,
            ],
            'null + boolean(true)' => [
                'boolean',
                null,
                true,
            ],
            'null + float' => [
                'float',
                null,
                true,
            ],
            'null + integer' => [
                'integer',
                null,
                true,
            ],
            'null + string' => [
                'string',
                null,
                true,
            ],
            'float + map' => [
                'map',
                1.337,
                false,
            ],
            'integer + boolean' => [
                'boolean',
                42,
                false,
            ],
            'array + float' => [
                'float',
                [],
                false,
            ],
            'string + integer' => [
                'integer',
                'John Doe',
                false,
            ],
            'boolean + string' => [
                'string',
                true,
                false,
            ],
            'object + string' => [
                'string',
                new \stdClass(),
                false,
            ],
            'array + list' => [
                'list',
                [],
                true,
            ],
            'integer + list' => [
                'list',
                1337,
                false,
            ],
            'array + tuple' => [
                'tuple',
                [],
                true,
            ],
            'integer + tuple' => [
                'tuple',
                1337,
                false,
            ],
            'array + numeric' => [
                'numeric',
                [],
                false,
            ],
            'string + numeric' => [
                'numeric',
                'foo',
                false,
            ],
            'numerical string + numeric' => [
                'numeric',
                '2',
                false,
            ],
            'double + numeric' => [
                'numeric',
                13.37,
                true,
            ],
            'integer + numeric' => [
                'numeric',
                1337,
                true,
            ],
            'boolean + numeric' => [
                'numeric',
                true,
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
        $args = 'integer';
        $data = 'foobar';
        $customMessage = 'This type is not valid';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError'])
            ->getMock();

        $context->enter('', (new Schema())->setRule(new Rule(TypeRule::ID, $args, $customMessage)));

        $context->expects($this->once())
            ->method('addError')
            ->with(TypeRule::ID, $customMessage);

        $typeRule = new TypeRule();

        $this->assertFalse($typeRule->performValidation($args, $data, $context));
    }
}

