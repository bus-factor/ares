<?php

declare(strict_types=1);

/**
 * SchemaTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace UnitTest\Ares\Schema;

use Ares\Schema\Rule;
use Ares\Schema\Schema;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class SchemaTest
 *
 * @coversDefaultClass \Ares\Schema\Schema
 */
class SchemaTest extends TestCase
{
    /**
     * @covers \Ares\Schema\Schema
     *
     * @return void
     */
    public function testRulesDefaults(): void
    {
        $schema = new Schema();

        $this->assertEquals([], $schema->getRules());
    }

    /**
     * @covers ::getRule
     * @covers ::hasRule
     *
     * @return void
     */
    public function testGetRule(): void
    {
        $rule = new Rule('foo', true);
        $schema = (new Schema())->setRule($rule);

        $this->assertSame($rule, $schema->getRule('foo'));
    }

    /**
     * @covers ::getRule
     * @covers ::hasRule
     *
     * @return void
     */
    public function testGetRuleHandlesInvalidRuleIds(): void
    {
        $schema = new Schema();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Rule not found in schema: foo');

        $schema->getRule('foo');
    }

    /**
     * @covers ::getRules
     * @covers ::setRule
     *
     * @dataProvider getSetRuleSamples
     *
     * @param Rule    $rule1        Schema rule.
     * @param Rule    $rule2        Schema rule.
     * @param boolean $replace      Replace parameter.
     * @param Rule    $expectedRule Schema rule.
     * @return void
     */
    public function testSetRule(Rule $rule1, Rule $rule2, bool $replace, Rule $expectedRule): void
    {
        $schema = new Schema();

        $this->assertSame($schema, $schema->setRule($rule1));
        $this->assertEquals(['foo' => $rule1], $schema->getRules());

        $this->assertSame($schema, $schema->setRule($rule2, $replace));
        $this->assertEquals(['foo' => $expectedRule], $schema->getRules());
    }

    /**
     * @return array
     */
    public static function getSetRuleSamples(): array
    {
        $rule1 = new Rule('foo', true);
        $rule2 = new Rule('foo', false);

        return [
            'replace=false' => [
                $rule1,
                $rule2,
                false,
                $rule1,
            ],
            'replace=true' => [
                $rule1,
                $rule2,
                true,
                $rule2,
            ],
        ];
    }

    /**
     * @covers ::getRules
     * @covers ::setRule
     * @covers ::setRules
     *
     * @return void
     */
    public function testSetRules(): void
    {
        $schema = new Schema();

        $rules = [
            new Rule('foo', true),
            new Rule('bar', false),
        ];

        $schema->setRules($rules);

        $this->assertEquals(
            [
                'foo' => $rules[0],
                'bar' => $rules[1],
            ],
            $schema->getRules()
        );
    }
}
