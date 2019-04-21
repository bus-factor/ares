<?php

declare(strict_types=1);

/**
 * UnknownAllowedRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\Error;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\TypeRule;
use Ares\Rule\UnknownAllowedRule;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\SchemaMap;
use Ares\Schema\Type;
use PHPUnit\Framework\TestCase;

/**
 * Class UnknownAllowedRuleTest
 *
 * @coversDefaultClass \Ares\Rule\UnknownAllowedRule
 */
class UnknownAllowedRuleTest extends TestCase
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
        $unknownAllowedRule = new UnknownAllowedRule();

        $this->assertInstanceOf($fqcn, $unknownAllowedRule);
    }

    /**
     * @covers ::getSupportedTypes
     *
     * @testWith ["map"]
     *           ["tuple"]
     *
     * @param string $type Supported type.
     * @return void
     */
    public function testGetSupportedTypes(string $type): void
    {
        $unknownAllowedRule = new UnknownAllowedRule();

        $this->assertContains($type, $unknownAllowedRule->getSupportedTypes());
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

        $unknownAllowedRule = new UnknownAllowedRule();

        $this->assertTrue($unknownAllowedRule->performValidation($args, $data, $context));
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
                    new Error(['', 'foo'], UnknownAllowedRule::ID, UnknownAllowedRule::ERROR_MESSAGE),
                    new Error(['', 'x'], UnknownAllowedRule::ID, UnknownAllowedRule::ERROR_MESSAGE),
                ],
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
        $args = false;
        $data = ['name' => 'John Doe', 'email' => 'john.doe@example.com'];
        $customMessage = 'Please do not add unknown fields';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError', 'getMessage'])
            ->getMock();

        $innerSchema = (new Schema())
            ->setRule(new Rule(TypeRule::ID, 'string'));

        $outerSchema = (new SchemaMap())
            ->setRule(new Rule(TypeRule::ID, 'map'))
            ->setRule(new Rule(UnknownAllowedRule::ID, false, $customMessage))
            ->setSchemas(['name' => $innerSchema]);

        $context->enter('', $outerSchema);

        $context->expects($this->once())
            ->method('addError')
            ->with(UnknownAllowedRule::ID, $customMessage);

        $unknownAllowedRule = new UnknownAllowedRule();

        $this->assertTrue($unknownAllowedRule->performValidation($args, $data, $context));
    }
}

