<?php

declare(strict_types=1);

/**
 * InvalidOptionExceptionTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-14
 */

namespace UnitTest\Ares\Exception;

use Ares\Exception\InvalidOptionException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class InvalidOptionExceptionTest
 *
 * @covers \Ares\Exception\InvalidOptionException
 */
class InvalidOptionExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $invalidOptionException = new InvalidOptionException();

        $this->assertInstanceOf(InvalidArgumentException::class, $invalidOptionException);
    }
}

