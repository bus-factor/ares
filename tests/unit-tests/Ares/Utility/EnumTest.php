<?php

declare(strict_types=1);

/**
 * EnumTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-02-21
 */

namespace UnitTest\Utility;

use PHPUnit\Framework\TestCase;
use Ares\Utility\Enum;

/**
 * Class EnumTest
 *
 * @coversDefaultClass \Ares\Utility\Enum
 */
class EnumTest extends TestCase
{
    /**
     * @covers ::getValues
     *
     * @return void
     */
    public function testGetValues(): void
    {
        $foobar = new class extends Enum {
            const FOO = 'foo';
            const BAR = 'bar';
        };

        $foobarClass = get_class($foobar);

        $this->assertEquals(['FOO' => 'foo', 'BAR' => 'bar'], $foobarClass::getValues());
    }
}

