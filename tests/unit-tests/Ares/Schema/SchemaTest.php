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
use PHPUnit\Framework\TestCase;

/**
 * Class SchemaTest
 *
 * @coversDefaultClass \Ares\Schema\Schema
 */
class SchemaTest extends TestCase
{
    /**
     * @return void
     */
    public function testRulesDefaults(): void
    {
        $schema = new Schema();

        $this->assertEquals([], $schema->getRules());
    }

    /**
     * @covers ::getRules
     * @covers ::setRule
     *
     * @dataProvider getSetRuleSamples
     *
     * @param \Ares\Schema\Rule $rule1        Schema rule.
     * @param \Ares\Schema\Rule $rule2        Schema rule.
     * @param boolean           $replace      Replace parameter.
     * @param \Ares\Schema\Rule $expectedRule Schema rule.
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
    public function getSetRuleSamples(): array
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
}

