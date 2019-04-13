<?php

declare(strict_types=1);

/**
 * SchemaMapTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace UnitTest\Ares\Schema;

use Ares\Schema\Schema;
use Ares\Schema\SchemaMap;
use PHPUnit\Framework\TestCase;

/**
 * Class SchemaMapTest
 *
 * @coversDefaultClass \Ares\Schema\SchemaMap
 */
class SchemaMapTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $schemaMap = new SchemaMap();

        $this->assertInstanceOf(Schema::class, $schemaMap);
    }

    /**
     * @covers ::getSchemas
     *
     * @return void
     */
    public function testSchemasDefault(): void
    {
        $schemaMap = new SchemaMap();

        $this->assertEquals([], $schemaMap->getSchemas());
    }

    /**
     * @covers ::getSchemas
     * @covers ::setSchema
     *
     * @return void
     */
    public function testSetSchema(): void
    {
        $schema = new Schema();

        $schemaMap = new SchemaMap();
        $schemaMap->setSchema('foo', $schema);

        $this->assertEquals(['foo' => $schema], $schemaMap->getSchemas());
    }
}

