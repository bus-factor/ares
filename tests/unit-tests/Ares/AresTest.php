<?php

declare(strict_types=1);

/**
 * AresTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-25
 */

namespace UnitTest\Ares;

use Ares\Ares;
use Ares\Sanitization\Sanitizer;
use Ares\Validation\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Class AresTest
 *
 * @coversDefaultClass \Ares\Ares
 */
class AresTest extends TestCase
{
    /**
     * @covers ::getSanitizer
     *
     * @return void
     */
    public function testGetSanitizer(): void
    {
        $schema = ['type' => 'integer'];
        $ares = new Ares($schema);

        $sanitizer = $ares->getSanitizer();

        $this->assertInstanceOf(Sanitizer::class, $sanitizer);
    }

    /**
     * @covers ::getValidator
     *
     * @return void
     */
    public function testGetValidator(): void
    {
        $schema = ['type' => 'integer'];
        $ares = new Ares($schema);

        $validator = $ares->getValidator();

        $this->assertInstanceOf(Validator::class, $validator);
    }

    /**
     * @covers ::sanitize
     *
     * @return void
     */
    public function testSanitize(): void
    {
        $data = ['a' => 'b'];
        $options = ['c' => 'd'];
        $dataSanitized = ['e' => 'f'];

        $sanitizer = $this->getMockBuilder(Sanitizer::class)
            ->disableOriginalConstructor()
            ->setMethods(['sanitize'])
            ->getMock();

        $sanitizer->expects($this->once())
            ->method('sanitize')
            ->with($data, $options)
            ->willReturn($dataSanitized);

        $ares = $this->getMockBuilder(Ares::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSanitizer'])
            ->getMock();

        $ares->expects($this->once())
            ->method('getSanitizer')
            ->willReturn($sanitizer);

        $this->assertSame($dataSanitized, $ares->sanitize($data, $options));
    }

    /**
     * @covers ::validate
     *
     * @testWith [true]
     *           [false]
     *
     * @param boolean $retVal Expected return value.
     * @return void
     */
    public function testValidate(bool $retVal): void
    {
        $data = ['a' => 'b'];
        $options = ['c' => 'd'];

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['validate'])
            ->getMock();

        $validator->expects($this->once())
            ->method('validate')
            ->with($data, $options)
            ->willReturn($retVal);

        $ares = $this->getMockBuilder(Ares::class)
            ->disableOriginalConstructor()
            ->setMethods(['getValidator'])
            ->getMock();

        $ares->expects($this->once())
            ->method('getValidator')
            ->willReturn($validator);

        $this->assertSame($retVal, $ares->validate($data, $options));
    }
}

