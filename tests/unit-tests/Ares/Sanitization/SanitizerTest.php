<?php

declare(strict_types=1);

/**
 * SanitizerTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-27
 */

namespace UnitTest\Ares\Sanitization;

use Ares\Exception\InvalidOptionException;
use Ares\Sanitization\Option;
use Ares\Sanitization\Sanitizer;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Validation\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class SanitizerTest
 *
 * @coversDefaultClass \Ares\Sanitization\Sanitizer
 */
class SanitizerTest extends TestCase
{
    /**
     * @covers ::prepareOptions
     * @covers ::sanitize
     *
     * @testWith [{}]
     *           [{"trimStrings": true}]
     *           [{"trimStrings": false}]
     *           [{"trimStrings": "foo"}, "Invalid sanitization option: 'trimStrings' must be of type <boolean>, got <string>"]
     *           [{"purgeUnknown": true}]
     *           [{"purgeUnknown": false}]
     *           [{"purgeUnknown": "foo"}, "Invalid sanitization option: 'purgeUnknown' must be of type <boolean>, got <string>"]
     *           [{"foo": true}, "Unknown sanitization option: 'foo' is not a supported sanitization option"]
     *
     * @return void
     */
    public function testSanitizeHandlesInvalidOptions(array $options, ?string $expectedExceptionMessage = null): void
    {
        $schema = (new Schema())->setRule(new Rule(TypeRule::ID, 'boolean'));
        $sanitizer = new Sanitizer($schema);

        if ($expectedExceptionMessage !== null) {
            $this->expectException(InvalidOptionException::class);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $sanitizer->sanitize(true, $options);

        $this->assertTrue(true);
    }

    /**
     * @covers ::performSanitization
     * @covers ::sanitize
     *
     * @return void
     */
    public function testSanitizeIgnoresUnknownTypes(): void
    {
        $schema = (new Schema())->setRule(new Rule(TypeRule::ID, 'unknownType'));
        $sanitizer = new Sanitizer($schema);

        $data = ['foo' => 'bar', false, 13.37];

        $this->assertSame($data, $sanitizer->sanitize($data));
    }
}

