<?php

declare(strict_types=1);

/**
 * StackEmptyExceptionTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-01
 */

namespace UnitTest\Ares\Exception;

use Ares\Exception\StackEmptyException;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Class StackEmptyExceptionTest
 *
 * @covers \Ares\Exception\StackEmptyException
 */
class StackEmptyExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $stackEmptyException = new StackEmptyException();

        $this->assertInstanceOf(LogicException::class, $stackEmptyException);
    }
}

