<?php

declare(strict_types=1);

/**
 * SchemaTupleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-18
 */

namespace UnitTest\Ares\Schema;

use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\SchemaTuple;
use PHPUnit\Framework\TestCase;

/**
 * Class SchemaTupleTest
 *
 * @coversDefaultClass \Ares\Schema\SchemaTuple
 */
class SchemaTupleTest extends TestCase
{
    /**
     * @covers \Ares\Schema\SchemaTuple
     *
     * @return void
     */
    public function testInstanceOf(): void
    {
        $schemaTuple = new SchemaTuple();

        $this->assertInstanceOf(Schema::class, $schemaTuple);
    }

    /**
     * @covers ::getSchemas
     *
     * @return void
     */
    public function testSchemasDefault(): void
    {
        $schemaTuple = new SchemaTuple();

        $this->assertEquals([], $schemaTuple->getSchemas());
    }

    /**
     * @covers ::appendSchema
     * @covers ::getSchemas
     *
     * @return void
     */
    public function testAppendSchema(): void
    {
        $schema1 = (new Schema())->setRule(new Rule('a', true));
        $schema2 = (new Schema())->setRule(new Rule('b', false));

        $schemaTuple = new SchemaTuple();

        $this->assertSame($schemaTuple, $schemaTuple->appendSchema($schema1));
        $this->assertEquals([$schema1], $schemaTuple->getSchemas());

        $this->assertSame($schemaTuple, $schemaTuple->appendSchema($schema2));
        $this->assertEquals([$schema1, $schema2], $schemaTuple->getSchemas());
    }

    /**
     * @covers ::appendSchema
     * @covers ::getSchemas
     * @covers ::setSchemas
     *
     * @return void
     */
    public function testSetSchemas(): void
    {
        $schemaTuple = new SchemaTuple();

        $schemas = [
            'foo' => (new Schema())->setRule(new Rule('a', true)),
            'bar' => (new Schema())->setRule(new Rule('b', false)),
        ];

        $schemaTuple->setSchemas($schemas);

        $this->assertEquals(
            [$schemas['foo'], $schemas['bar']],
            $schemaTuple->getSchemas()
        );
    }
}

