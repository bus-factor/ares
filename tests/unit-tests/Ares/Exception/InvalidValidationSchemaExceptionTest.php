<?php

declare(strict_types=1);

/**
 * InvalidValidationSchemaExceptionTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

namespace UnitTest\Ares\Exception;

use Ares\Exception\InvalidValidationSchemaException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class InvalidValidationSchemaExceptionTest
 *
 * @coversDefaultClass \Ares:Exception\InvalidValidationSchemaException
 */
class InvalidValidationSchemaExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $invalidValidationSchemaException = new InvalidValidationSchemaException();

        $this->assertInstanceOf(InvalidArgumentException::class, $invalidValidationSchemaException);
    }
}

