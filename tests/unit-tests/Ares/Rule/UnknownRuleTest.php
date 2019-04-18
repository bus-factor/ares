<?php

declare(strict_types=1);

/**
 * UnknownRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\Error;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\UnknownRule;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\SchemaMap;
use Ares\Schema\Type;
use PHPUnit\Framework\TestCase;

/**
 * Class UnknownRuleTest
 *
 * @coversDefaultClass \Ares\Rule\UnknownRule
 */
class UnknownRuleTest extends TestCase
{
    /**
     * @testWith ["Ares\\Rule\\RuleInterface"]
     *           ["Ares\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $unknownRule = new UnknownRule();

        $this->assertInstanceOf($fqcn, $unknownRule);
    }

    /**
     * @covers ::getSupportedTypes
     *
     * @testWith ["map"]
     *
     * @param string $type Supported type.
     * @return void
     */
    public function testGetSupportedTypes(string $type): void
    {
        $unknownRule = new UnknownRule();

        $this->assertContains($type, $unknownRule->getSupportedTypes());
    }

    /**
     * @covers ::performValidation
     *
     * @dataProvider getValidateSamples
     *
     * @param bool                $args           Validation rule configuration.
     * @param mixed               $data           Validated data.
     * @param \Ares\Schema\Schema $schema         Validation schema.
     * @param array               $expectedErrors Validation errors.
     * @return void
     */
    public function testValidate(bool $args, $data, Schema $schema, array $expectedErrors): void
    {
        $context = new Context($data, new ErrorMessageRenderer());
        $context->enter('', $schema);

        $unknownRule = new UnknownRule();

        $this->assertTrue($unknownRule->performValidation($args, $data, $context));
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
                (new SchemaMap())
                    ->setRule(new Rule('type', Type::MAP))
                    ->setSchemas([
                    ]),
                [],
            ],
            'unknown not allowed' => [
                false,
                [
                    'foo' => 'bar',
                    'fizz' => 'buzz',
                    'x' => 'y',
                ],
                (new SchemaMap())
                    ->setRule(new Rule('type', Type::MAP))
                    ->setSchemas([
                        'fizz' => (new Schema())->setRule(new Rule('type', Type::STRING)),
                    ]),
                [
                    new Error(['', 'foo'], UnknownRule::ID, UnknownRule::ERROR_MESSAGE),
                    new Error(['', 'x'], UnknownRule::ID, UnknownRule::ERROR_MESSAGE),
                ],
            ],
        ];
    }
}

