<?php

declare(strict_types=1);

/**
 * OptionTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace UnitTest\Ares\Validation;

use Ares\Validation\Option;
use PHPUnit\Framework\TestCase;

/**
 * Class OptionTest
 *
 * @covers \Ares\Validation\Option
 */
class OptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstants(): void
    {
        $expectedValues = [
            'ALL_UNKNOWN_ALLOWED' => 'allUnknownAllowed',
            'ALL_BLANKABLE'       => 'allBlankable',
            'ALL_NULLABLE'        => 'allNullable',
            'ALL_REQUIRED'        => 'allRequired',
        ];

        $this->assertEquals($expectedValues, Option::getValidValues());
    }
}

