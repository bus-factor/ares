<?php

declare(strict_types=1);

/**
 * SanitizerTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-10
 */

namespace UnitTest\Ares\Schema;

use Ares\Exception\InvalidValidationSchemaException;
use Ares\Schema\Sanitizer;
use PHPUnit\Framework\TestCase;

/**
 * Class SanitizerTest
 *
 * @coversDefaultClass \Ares\Schema\Sanitizer
 */
class SanitizerTest extends TestCase
{
    /**
     * @covers ::sanitize
     * @covers ::performSanitization
     *
     * @dataProvider getSanitizeSamples
     *
     * @param array       $schema                   Validation schema.
     * @param array       $schemaDefaults           Validation schema defaults.
     * @param array|null  $expectedRetVal           Expected return value.
     * @param string|null $expectedException        Expected exception FQCN.
     * @param string|null $expectedExceptionMessage Expected exception message.
     * @return void
     */
    public function testSanitize(
        array $schema,
        array $schemaDefaults,
        ?array $expectedRetVal,
        ?string $expectedException = null,
        ?string $expectedExceptionMessage = null
    ): void {
        if ($expectedException === null) {
            $this->assertEquals($expectedRetVal, Sanitizer::sanitize($schema, $schemaDefaults));
        } else {
            $this->expectException($expectedException);
            $this->expectExceptionMessage($expectedExceptionMessage);

            Sanitizer::sanitize($schema, $schemaDefaults);
        }
    }

    /**
     * @return array
     */
    public function getSanitizeSamples(): array
    {
        return [
            'missing "type" option (top level)' => [
                [],
                [],
                null,
                InvalidValidationSchemaException::class,
                'Missing schema option: $schema[\'type\']',
            ],
            'missing "type" option (nested)' => [
                ['type' => 'map', 'schema' => ['name' => []]],
                [],
                null,
                InvalidValidationSchemaException::class,
                'Missing schema option: $schema[\'schema\'][\'name\'][\'type\']',
            ],
            'invalid schema data type' => [
                ['type' => 'map', 'schema' => 42],
                [],
                null,
                InvalidValidationSchemaException::class,
                'Expected <array>, got <integer>: $schema[\'schema\']',
            ],
            'defaults set (top level)' => [
                ['type' => 'integer'],
                ['required' => true],
                ['type' => 'integer', 'required' => true],
            ],
            'defaults set (nested)' => [
                [
                    'type' => 'map',
                    'schema' => [
                        'name' => ['type' => 'string', 'required' => false],
                        'email' => ['type' => 'string', 'blankable' => true],
                    ],
                ],
                ['required' => true, 'blankable' => false],
                [
                    'type' => 'map',
                    'schema' => [
                        'name' => ['type' => 'string', 'required' => false, 'blankable' => false],
                        'email' => ['type' => 'string', 'blankable' => true, 'required' => true],
                    ],
                    'required' => true,
                    'blankable' => false,
                ],
            ],
            'invalid type (top level)' => [
                ['type' => 'foo'],
                [],
                null,
                InvalidValidationSchemaException::class,
                'Invalid schema option value: $schema[\'type\'] = \'foo\'',
            ],
            'invalid type (nested)' => [
                ['type' => 'map', 'schema' => ['name' => ['type' => 'fizz']]],
                [],
                null,
                InvalidValidationSchemaException::class,
                'Invalid schema option value: $schema[\'schema\'][\'name\'][\'type\'] = \'fizz\'',
            ],
            'missing schema option for map (top level)' => [
                ['type' => 'map'],
                [],
                ['type' => 'map', 'schema' => []],
            ],
            'missing schema option for map (nested)' => [
                ['type' => 'map', 'schema' => ['meta' => ['type' => 'map']]],
                [],
                ['type' => 'map', 'schema' => ['meta' => ['type' => 'map', 'schema' => []]]],
            ],
            'missing schema option for list (top level)' => [
                ['type' => 'list'],
                [],
                null,
                InvalidValidationSchemaException::class,
                'Missing schema option: $schema[\'schema\']',
            ],
            'missing schema option for list (nested)' => [
                ['type' => 'map', 'schema' => ['meta' => ['type' => 'list']]],
                [],
                null,
                InvalidValidationSchemaException::class,
                'Missing schema option: $schema[\'schema\'][\'meta\'][\'schema\']',
            ],
            'defaults set for list' => [
                ['type' => 'list', 'schema' => ['type' => 'integer']],
                ['required' => true],
                ['type' => 'list', 'schema' => ['type' => 'integer', 'required' => true], 'required' => true],
            ],
        ];
    }
}

