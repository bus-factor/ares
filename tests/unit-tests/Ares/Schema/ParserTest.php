<?php

declare(strict_types=1);

/**
 * ParserTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-07
 */

namespace UnitTest\Ares\Schema;

use Ares\Exception\InvalidSchemaException;
use Ares\Schema\Parser;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\SchemaList;
use Ares\Schema\SchemaMap;
use Ares\Schema\SchemaReference;
use Ares\Schema\SchemaTuple;
use Ares\Schema\TypeRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Class ParserTest
 *
 * @coversDefaultClass \Ares\Schema\Parser
 */
class ParserTest extends TestCase
{
    /**
     * @covers ::ascertainInputHoldsArrayOrFail
     * @covers ::extractTypeOrFail
     * @covers ::fail
     * @covers ::parse
     * @covers ::parseRule
     * @covers ::parseRuleWithAdditions
     * @covers ::parseSchema
     * @covers ::parseSchemas
     * @covers ::parseTupleSchemas
     * @covers ::prepareSchemaInstance
     *
     * @dataProvider getParseErrorHandlingSamples
     *
     * @param mixed       $schemaIn                 Provided schema.
     * @param array|null  $expectedSchemaOut        Expected resulting schema.
     * @param string|null $expectedException        Expected exception.
     * @param string|null $expectedExceptionMessage Expected exception message.
     * @return void
     */
    public function testParseErrorHandling(
        $schemaIn,
        ?array $expectedSchemaOut = null,
        ?string $expectedException = null,
        ?string $expectedExceptionMessage = null
    ): void {
        $parser = new Parser();

        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        if ($expectedExceptionMessage !== null) {
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $schemaOut = $parser->parse($schemaIn);

        if ($expectedSchemaOut !== null) {
            $this->assertEquals($expectedSchemaOut, $schemaOut);
        }
    }

    /**
     * @return array
     */
    public function getParseErrorHandlingSamples(): array
    {
        return [
            'invalid schema' => [
                'foobar',
                null,
                InvalidSchemaException::class,
                'Invalid schema value:  must be of type <array>, got <string>',
            ],
            'multiple types' => [
                [
                    'type' => 'integer',
                    ['type' => 'integer'],
                ],
                null,
                InvalidSchemaException::class,
                'Ambiguous schema:  contains multiple `type` validation rules',
            ],
            'missing type' => [
                [],
                null,
                InvalidSchemaException::class,
                'Insufficient schema:  contains no `type` validation rule',
            ],
            'invalid type' => [
                [
                    'type' => 'foo',
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema:  uses unknown type: "foo"',
            ],
            'invalid schema value' => [
                [
                    'type' => 'string',
                    'foobar',
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /0 must be of type <array>, got <string>',
            ],
            'type "list" without "schema"' => [
                [
                    'type' => 'list',
                ],
                null,
                InvalidSchemaException::class,
                'Missing schema key:  uses type "list" but contains no "schema" key',
            ],
            'nested type "list" without "schema"' => [
                [
                    'type' => 'list',
                    'schema' => [
                        'type' => 'list',
                    ],
                ],
                null,
                InvalidSchemaException::class,
                'Missing schema key: /schema uses type "list" but contains no "schema" key',
            ],
            'type "map" without "schema"' => [
                [
                    'type' => 'map',
                ],
                null,
                InvalidSchemaException::class,
                'Missing schema key:  uses type "map" but contains no "schema" key',
            ],
            'nested type "map" without "schema"' => [
                [
                    'type' => 'map',
                    'schema' => [
                        'name' => [
                            'type' => 'map',
                        ],
                    ],
                ],
                null,
                InvalidSchemaException::class,
                'Missing schema key: /schema/name uses type "map" but contains no "schema" key',
            ],
            'type "list" with non-array schema value' => [
                [
                    'type' => 'list',
                    'schema' => 'foo',
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /schema must be of type <array>, got <string>',
            ],
            'nested type "list" with non-array schema value' => [
                [
                    'type' => 'list',
                    'schema' => [
                        'type' => 'list',
                        'schema' => 13.37,
                    ],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /schema/schema must be of type <array>, got <double>',
            ],
            'type "map" with non-array schema value' => [
                [
                    'type' => 'map',
                    'schema' => 'foo',
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /schema must be of type <array>, got <string>',
            ],
            'nested type "map" with non-array schema value' => [
                [
                    'type' => 'map',
                    'schema' => [
                        'name' => [
                            'type' => 'map',
                            'schema' => false,
                        ],
                    ],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /schema/name/schema must be of type <array>, got <boolean>',
            ],
            'invalid schema value (nested)' => [
                [
                    'type' => 'map',
                    'schema' => [
                        'name' => [
                            'type' => 'integer',
                            1337,
                        ],
                    ],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /schema/name/0 must be of type <array>, got <integer>',
            ],
            'unknown rule' => [
                [
                    'type' => 'integer',
                    'whooops' => [1, 2, 3],
                ],
                null,
                InvalidSchemaException::class,
                'Unknown validation rule ID: /whooops specifies an unknown validation rule ID',
            ],
            'nested unknown rule' => [
                [
                    'type' => 'list',
                    'schema' => [
                        'type' => 'string',
                        'whooops' => [1, 2, 3],
                    ],
                ],
                null,
                InvalidSchemaException::class,
                'Unknown validation rule ID: /schema/whooops specifies an unknown validation rule ID',
            ],
            'unknown rule with additions' => [
                [
                    'type' => 'integer',
                    ['whooops' => [1, 2, 3]],
                ],
                null,
                InvalidSchemaException::class,
                'Unknown validation rule ID: /0/whooops specifies an unknown validation rule ID',
            ],
            'nested unknown rule with additions' => [
                [
                    'type' => 'list',
                    'schema' => [
                        'type' => 'string',
                        ['whooops' => [1, 2, 3]],
                    ],
                ],
                null,
                InvalidSchemaException::class,
                'Unknown validation rule ID: /schema/0/whooops specifies an unknown validation rule ID',
            ],
            'rule with additions with missing rule' => [
                [
                    'type' => 'string',
                    [],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema: /0 contains no rule',
            ],
            'nested rule with additions with missing rule' => [
                [
                    'type' => 'list',
                    'schema' => [
                        'type' => 'string',
                        [],
                    ],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema: /schema/0 contains no rule',
            ],
            'rule with additions with ambiguous rule' => [
                [
                    'type' => 'string',
                    ['required' => false, 'blankable' => true],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema: /0 contains multiple rules (["required","blankable"])',
            ],
            'nested rule with additions with ambiguous rule' => [
                [
                    'type' => 'list',
                    'schema' => [
                        'type' => 'string',
                        ['required' => false, 'blankable' => true],
                    ],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema: /schema/0 contains multiple rules (["required","blankable"])',
            ],
            'invalid "message" data type' => [
                [
                    'type' => 'boolean',
                    ['nullable' => true, 'message' => 13.37],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /0/message must be of type <string>, got <double>',
            ],
            'invalid "meta" data type' => [
                [
                    'type' => 'float',
                    ['required' => true, 'meta' => 'foobar'],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /0/meta must be of type <array>, got <string>',
            ],
            'inapplicable rule' => [
                [
                    'type' => 'integer',
                    'blankable' => true,
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema: /blankable validation rule is not applicable to type <integer>',
            ],
        ];
    }

    /**
     * @covers ::ascertainInputHoldsArrayOrFail
     * @covers ::extractTypeOrFail
     * @covers ::fail
     * @covers ::parse
     * @covers ::parseRule
     * @covers ::parseRuleWithAdditions
     * @covers ::parseSchema
     * @covers ::parseSchemas
     * @covers ::parseTupleSchemas
     * @covers ::prepareCustomTypeSchemaInstance
     * @covers ::prepareSchemaInstance
     *
     * @return void
     */
    public function testParse(): void
    {
        TypeRegistry::register('Email', [
            'type' => 'string',
            'email' => true,
        ]);

        TypeRegistry::register('RecursiveType', [
            'type' => 'map',
            'schema' => [
                'recursiveReference' => [
                    'type' => 'RecursiveType',
                ],
            ],
        ]);

        $parser = new Parser();

        $schema = $parser->parse([
            'type' => 'map',
            ['nullable' => true, 'message' => 'Not nullable'],
            'schema' => [
                'tags' => [
                    ['type' => 'list', 'message' => 'Must be a list', 'meta' => ['foo' => 'bar']],
                    'schema' => [
                        'type' => 'integer',
                        ['min' => 23, 'message' => 'Must be at least 23'],
                    ],
                ],
                'tuple' => [
                    ['type' => 'tuple', 'message' => 'Must be a tuple'],
                    'schema' => [
                        [
                            'type' => 'integer',
                        ],
                        [
                            'type' => 'string',
                            'required' => false,
                        ],
                    ],
                ],
                'email' => [
                    'type' => 'Email',
                    'nullable' => true,
                ],
                'recursiveReference' => [
                    'type' => 'RecursiveType'
                ],
            ],
        ]);

        $recursiveReference = new SchemaReference();

        $recursiveReferenceMap = (new SchemaMap())
            ->setRule(new Rule('type', 'map'))
            ->setSchemas([
                'recursiveReference' => $recursiveReference
            ]);

        $recursiveReference->setSchema($recursiveReferenceMap);

        $this->assertEquals(
            (new SchemaMap())
                ->setRules([
                    'type' => new Rule('type', 'map'),
                    'nullable' => new Rule('nullable', true, 'Not nullable'),
                ])
                ->setSchemas([
                    'tags' => (new SchemaList())
                        ->setRules([
                            'type' => new Rule('type', 'list', 'Must be a list', ['foo' => 'bar']),
                        ])
                        ->setSchema(
                            (new Schema())
                                ->setRules([
                                    'type' => new Rule('type', 'integer'),
                                    'min' => new Rule('min', 23, 'Must be at least 23'),
                                ])
                        ),
                    'tuple' => (new SchemaTuple())
                        ->setRules([
                            'type' => new Rule('type', 'tuple', 'Must be a tuple'),
                            'unknownAllowed' => new Rule('unknownAllowed', false),
                        ])
                        ->setSchemas([
                            (new Schema())
                                ->setRules([
                                    'type' => new Rule('type', 'integer'),
                                    'required' => new Rule('required', true),
                                ]),
                            (new Schema())
                                ->setRules([
                                    'type' => new Rule('type', 'string'),
                                    'required' => new Rule('required', true),
                                ]),
                        ]),
                    'email' => TypeRegistry::get('Email')
                        ->setRule(new Rule('nullable', true)),
                    'recursiveReference' => $recursiveReferenceMap,
                ]),
            $schema
        );
    }
}

