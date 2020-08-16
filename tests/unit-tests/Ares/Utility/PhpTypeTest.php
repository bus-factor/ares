<?php

declare(strict_types=1);

/**
 * PhpTypeTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace UnitTest\Ares\Utility;

use Ares\Utility\PhpType;
use PHPUnit\Framework\TestCase;

/**
 * Class PhpTypeTest
 *
 * @covers \Ares\Utility\PhpType
 */
class PhpTypeTest extends TestCase
{
    /**
     * @testWith ["ARRAY", "array"]
     *           ["BOOLEAN", "boolean"]
     *           ["DOUBLE", "double"]
     *           ["INTEGER", "integer"]
     *           ["NULL", "NULL"]
     *           ["STRING", "string"]
     *
     * @param string $constantName  Constant name.
     * @param string $constantValue Constant value.
     * @return void
     */
    public function testValues(string $constantName, string $constantValue): void
    {
        $values = PhpType::getValues();

        $this->assertArrayHasKey($constantName, $values);
        $this->assertSame($constantValue, $values[$constantName]);
    }
}

