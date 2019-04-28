<?php

declare(strict_types=1);

/**
 * SchemaReferenceTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-28
 */

namespace UnitTest\Ares\Schema;

use Ares\Schema\Schema;
use Ares\Schema\SchemaReference;
use PHPUnit\Framework\TestCase;

/**
 * Class SchemaReferenceTest
 *
 * @coversDefaultClass \Ares\Schema\SchemaReference
 */
class SchemaReferenceTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $schemaReference = new SchemaReference();

        $this->assertInstanceOf(Schema::class, $schemaReference);
    }

    /**
     * @covers ::getSchema
     * @covers ::setSchema
     *
     * @return void
     */
    public function testSchemaAccessors(): void
    {
        $schema = new Schema();
        $schemaReference = new SchemaReference();

        $this->assertSame($schemaReference, $schemaReference->setSchema($schema));
        $this->assertSame($schema, $schemaReference->getSchema());
    }
}

