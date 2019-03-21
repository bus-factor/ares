<?php

declare(strict_types=1);

/**
 * UnknownRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace UnitTest\Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Validation\Context;
use Ares\Validation\Error;
use Ares\Validation\Rule\UnknownRule;
use PHPUnit\Framework\TestCase;

/**
 * Class UnknownRuleTest
 *
 * @coversDefaultClass \Ares\Validation\Rule\UnknownRule
 */
class UnknownRuleTest extends TestCase
{
    /**
     * @covers ::validate
     *
     * @dataProvider getValidateSamples
     *
     * @param bool  $args           Validation rule configuration.
     * @param mixed $data           Validated data.
     * @param array $schema         Validation schema.
     * @param array $expectedErrors Validation errors.
     * @return void
     */
    public function testValidate(bool $args, $data, array $schema, array $expectedErrors): void
    {
        $context = new Context();
        $context->enter('', $schema);

        $unknownRule = new UnknownRule();

        $this->assertTrue($unknownRule->validate($args, $data, $context));
        $this->assertEquals($expectedErrors, $context->getErrors());
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
    {
        return [
            'unknown allowed' => [
                true,
                ['foo' => 'bar'],
                [
                    'type' => 'map',
                    'schema' => [],
                ],
                [],
            ],
            'unknown not allowed' => [
                false,
                [
                    'foo' => 'bar',
                    'fizz' => 'buzz',
                    'x' => 'y',
                ],
                [
                    'type' => 'map',
                    'schema' => [
                        'fizz' => [
                            'type' => 'string',
                        ],
                    ],
                ],
                [
                    new Error(['', 'foo'], UnknownRule::ID, UnknownRule::ERROR_MESSAGE),
                    new Error(['', 'x'], UnknownRule::ID, UnknownRule::ERROR_MESSAGE),
                ],
            ],
            'unknown not allowed w/ data type mismatch' => [
                false,
                'foobar',
                [
                    'type' => 'map',
                    'schema' => [
                        'fizz' => [
                            'type' => 'string',
                        ],
                    ],
                ],
                [],
            ],
        ];
    }
}

