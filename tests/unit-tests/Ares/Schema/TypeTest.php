<?php

declare(strict_types=1);

/**
 * TypeTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace UnitTest\Ares\Schema;

use Ares\Schema\Type;
use PHPUnit\Framework\TestCase;

/**
 * Class TypeTest
 *
 * @covers \Ares\Schema\Type
 */
class TypeTest extends TestCase
{
    /**
     * @testWith ["BOOLEAN", "boolean"]
     *           ["FLOAT", "float"]
     *           ["INTEGER", "integer"]
     *           ["LIST", "list"]
     *           ["MAP", "map"]
     *           ["NUMERIC", "numeric"]
     *           ["STRING", "string"]
     *           ["TUPLE", "tuple"]
     *
     * @param string $constantName  Constant name.
     * @param string $constantValue Constant value.
     * @return void
     */
    public function testValues(string $constantName, string $constantValue): void
    {
        $values = Type::getValidValues();

        $this->assertArrayHasKey($constantName, $values);
        $this->assertSame($constantValue, $values[$constantName]);
    }
}

