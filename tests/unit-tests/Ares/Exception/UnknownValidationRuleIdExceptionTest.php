<?php

declare(strict_types=1);

/**
 * UnknownValidationRuleIdExceptionTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

namespace UnitTest\Ares\Exception;

use Ares\Exception\UnknownValidationRuleIdException;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Class UnknownValidationRuleIdExceptionTest
 *
 * @covers \Ares\Exception\UnknownValidationRuleIdException
 */
class UnknownValidationRuleIdExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $unknownValidationRuleIdException = new UnknownValidationRuleIdException();

        $this->assertInstanceOf(LogicException::class, $unknownValidationRuleIdException);
    }
}

