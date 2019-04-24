<?php

declare(strict_types=1);

/**
 * InvalidValidationOptionExceptionTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-14
 */

namespace UnitTest\Ares\Exception;

use Ares\Exception\InvalidValidationOptionException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class InvalidValidationOptionExceptionTest
 *
 * @coversDefaultClass \Ares\Exception\InvalidValidationOptionException
 */
class InvalidValidationOptionExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $invalidValidationOptionException = new InvalidValidationOptionException();

        $this->assertInstanceOf(InvalidArgumentException::class, $invalidValidationOptionException);
    }
}

