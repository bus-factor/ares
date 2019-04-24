<?php

declare(strict_types=1);

/**
 * InvalidSchemaExceptionTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

namespace UnitTest\Ares\Exception;

use Ares\Exception\InvalidSchemaException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class InvalidSchemaExceptionTest
 *
 * @coversDefaultClass \Ares\Exception\InvalidSchemaException
 */
class InvalidSchemaExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $invalidSchemaException = new InvalidSchemaException();

        $this->assertInstanceOf(InvalidArgumentException::class, $invalidSchemaException);
    }
}

