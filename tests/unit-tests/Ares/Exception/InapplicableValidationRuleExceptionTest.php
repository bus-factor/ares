<?php

declare(strict_types=1);

/**
 * InapplicableValidationRuleExceptionTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

namespace UnitTest\Ares\Exception;

use Ares\Exception\InapplicableValidationRuleException;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Class InapplicableValidationRuleExceptionTest
 *
 * @coversDefaultClass \Ares:Exception\InapplicableValidationRuleException
 */
class InapplicableValidationRuleExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $inapplicableValidationRuleException = new InapplicableValidationRuleException();

        $this->assertInstanceOf(LogicException::class, $inapplicableValidationRuleException);
    }
}

