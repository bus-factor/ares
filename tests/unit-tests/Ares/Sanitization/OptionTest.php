<?php

declare(strict_types=1);

/**
 * OptionTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-27
 */

namespace UnitTest\Ares\Sanitization;

use Ares\Sanitization\Option;
use PHPUnit\Framework\TestCase;

/**
 * Class OptionTest
 *
 * @covers \Ares\Sanitization\Option
 */
class OptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstants(): void
    {
        $expectedValues = [
            'TRIM_STRINGS'  => 'trimStrings',
            'PURGE_UNKNOWN' => 'purgeUnknown',
        ];

        $actualValues = array_combine(
            array_map(fn (Option $option): string => $option->name, Option::cases()),
            array_map(fn (Option $option): string => $option->value, Option::cases()),
        );

        $this->assertEquals($expectedValues, $actualValues);
    }
}
