<?php

declare(strict_types=1);

/**
 * SchemaListTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace UnitTest\Ares\Schema;

use Ares\Schema\Schema;
use Ares\Schema\SchemaList;
use PHPUnit\Framework\TestCase;

/**
 * Class SchemaListTest
 *
 * @coversDefaultClass \Ares\Schema\SchemaList
 */
class SchemaListTest extends TestCase
{
    /**
     * @covers \Ares\Schema\SchemaList
     *
     * @return void
     */
    public function testInstanceOf(): void
    {
        $schemaList = new SchemaList();

        $this->assertInstanceOf(Schema::class, $schemaList);
    }

    /**
     * @covers ::getSchema
     *
     * @return void
     */
    public function testSchemaDefault(): void
    {
        $schemaList = new SchemaList();

        $this->assertNull($schemaList->getSchema());
    }

    /**
     * @covers ::getSchema
     * @covers ::setSchema
     *
     * @return void
     */
    public function testSetSchema(): void
    {
        $schema = new Schema();

        $schemaList = new SchemaList();
        $schemaList->setSchema($schema);

        $this->assertSame($schema, $schemaList->getSchema());
    }
}

