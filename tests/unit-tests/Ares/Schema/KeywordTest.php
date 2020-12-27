<?php

declare(strict_types=1);

/**
 * KeywordTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace UnitTest\Ares\Schema;

use Ares\Schema\Keyword;
use PHPUnit\Framework\TestCase;

/**
 * Class KeywordTest
 *
 * @covers \Ares\Schema\Keyword
 */
class KeywordTest extends TestCase
{
    /**
     * @testWith ["MESSAGE", "message"]
     *           ["META", "meta"]
     *           ["SCHEMA", "schema"]
     *
     * @param string $constantName  Constant name.
     * @param string $constantValue Constant value.
     * @return void
     */
    public function testValues(string $constantName, string $constantValue): void
    {
        $values = Keyword::getValidValues();

        $this->assertArrayHasKey($constantName, $values);
        $this->assertSame($constantValue, $values[$constantName]);
    }
}

