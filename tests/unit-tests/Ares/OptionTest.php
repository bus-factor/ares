<?php

declare(strict_types=1);

/**
 * OptionTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace UnitTest\Ares;

use Ares\Option;
use PHPUnit\Framework\TestCase;

/**
 * Class OptionTest
 *
 * @coversDefaultClass \Ares\Option
 */
class OptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstants(): void
    {
        $expectedValues = [
            'ALLOW_UNKNOWN' => 'allowUnknown',
            'ALL_BLANKABLE' => 'allBlankable',
            'ALL_NULLABLE' => 'allNullable',
            'ALL_REQUIRED' => 'allRequired',
        ];

        $this->assertEquals($expectedValues, Option::getValues());
    }
}

