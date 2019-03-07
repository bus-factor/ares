<?php

declare(strict_types=1);

/**
 * TypeTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace UnitTest\Ares\Validation\Schema;

use Ares\Validation\Schema\Type;
use PHPUnit\Framework\TestCase;

/**
 * Class TypeTest
 *
 * @coversDefaultClass \Ares\Validation\Schema\Type
 */
class TypeTest extends TestCase
{
    /**
     * @testWith ["BOOLEAN", "boolean"]
     *           ["FLOAT", "float"]
     *           ["INTEGER", "integer"]
     *           ["STRING", "string"]
     *
     * @param string $constantName  Constant name.
     * @param string $constantValue Constant value.
     * @return void
     */
    public function testValues(string $constantName, string $constantValue): void
    {
        $values = Type::getValues();

        $this->assertArrayHasKey($constantName, $values);
        $this->assertSame($constantValue, $values[$constantName]);
    }
}

