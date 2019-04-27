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
use Ares\Schema\SchemaTuple;
use Ares\Validation\RuleFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class ParserTest
 *
 * @coversDefaultClass \Ares\Schema\Parser
 */
class ParserTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::ascertainInputHoldsArrayOrFail
     * @covers ::extractTypeOrFail
     * @covers ::fail
     * @covers ::parse
     * @covers ::parseRule
     * @covers ::parseRuleWithAdditions
     * @covers ::parseSchema
     * @covers ::parseSchemas
     * @covers ::parseTupleSchemas
     *
     * @dataProvider getParseErrorHandlingSamples
     *
     * @param RuleFactory $ruleFactory              Validation rule factory.
     * @param mixed       $schemaIn                 Provided schema.
     * @param array|null  $expectedSchemaOut        Expected resulting schema.
     * @param string|null $expectedException        Expected exception.
     * @param string|null $expectedExceptionMessage Expected exception message.
     * @return void
     */
    public function testParseErrorHandling(
        RuleFactory $ruleFactory,
        $schemaIn,
        ?array $expectedSchemaOut = null,
        ?string $expectedException = null,
        ?string $expectedExceptionMessage = null
    ): void {
        //$ruleFactory = new RuleFactory();
        $parser = new Parser($ruleFactory);

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
                new RuleFactory(),
                'foobar',
                null,
                InvalidSchemaException::class,
                'Invalid schema value:  must be of type <array>, got <string>',
            ],
            'multiple types' => [
                new RuleFactory(),
                [
                    'type' => 'integer',
                    ['type' => 'integer'],
                ],
                null,
                InvalidSchemaException::class,
                'Ambiguous schema:  contains multiple `type` validation rules',
            ],
            'missing type' => [
                new RuleFactory(),
                [],
                null,
                InvalidSchemaException::class,
                'Insufficient schema:  contains no `type` validation rule',
            ],
            'invalid type' => [
                new RuleFactory(),
                [
                    'type' => 'foo',
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema:  uses unknown type: "foo"',
            ],
            'invalid schema value' => [
                new RuleFactory(),
                [
                    'type' => 'string',
                    'foobar',
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /0 must be of type <array>, got <string>',
            ],
            'type "list" without "schema"' => [
                new RuleFactory(),
                [
                    'type' => 'list',
                ],
                null,
                InvalidSchemaException::class,
                'Missing schema key:  uses type "list" but contains no "schema" key',
            ],
            'nested type "list" without "schema"' => [
                new RuleFactory(),
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
                new RuleFactory(),
                [
                    'type' => 'map',
                ],
                null,
                InvalidSchemaException::class,
                'Missing schema key:  uses type "map" but contains no "schema" key',
            ],
            'nested type "map" without "schema"' => [
                new RuleFactory(),
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
                new RuleFactory(),
                [
                    'type' => 'list',
                    'schema' => 'foo',
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /schema must be of type <array>, got <string>',
            ],
            'nested type "list" with non-array schema value' => [
                new RuleFactory(),
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
                new RuleFactory(),
                [
                    'type' => 'map',
                    'schema' => 'foo',
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /schema must be of type <array>, got <string>',
            ],
            'nested type "map" with non-array schema value' => [
                new RuleFactory(),
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
                new RuleFactory(),
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
                new RuleFactory(),
                [
                    'type' => 'integer',
                    'whooops' => [1, 2, 3],
                ],
                null,
                InvalidSchemaException::class,
                'Unknown validation rule ID: /whooops specifies an unknown validation rule ID',
            ],
            'nested unknown rule' => [
                new RuleFactory(),
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
                new RuleFactory(),
                [
                    'type' => 'integer',
                    ['whooops' => [1, 2, 3]],
                ],
                null,
                InvalidSchemaException::class,
                'Unknown validation rule ID: /0/whooops specifies an unknown validation rule ID',
            ],
            'nested unknown rule with additions' => [
                new RuleFactory(),
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
                new RuleFactory(),
                [
                    'type' => 'string',
                    [],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema: /0 contains no rule',
            ],
            'nested rule with additions with missing rule' => [
                new RuleFactory(),
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
                new RuleFactory(),
                [
                    'type' => 'string',
                    ['required' => false, 'blankable' => true],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema: /0 contains multiple rules (["required","blankable"])',
            ],
            'nested rule with additions with ambiguous rule' => [
                new RuleFactory(),
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
                new RuleFactory(),
                [
                    'type' => 'boolean',
                    ['nullable' => true, 'message' => 13.37],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /0/message must be of type <string>, got <double>',
            ],
            'invalid "meta" data type' => [
                new RuleFactory(),
                [
                    'type' => 'float',
                    ['required' => true, 'meta' => 'foobar'],
                ],
                null,
                InvalidSchemaException::class,
                'Invalid schema value: /0/meta must be of type <array>, got <string>',
            ],
            'inapplicable rule' => [
                new RuleFactory(),
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
     * @covers ::__construct
     * @covers ::ascertainInputHoldsArrayOrFail
     * @covers ::extractTypeOrFail
     * @covers ::fail
     * @covers ::parse
     * @covers ::parseRule
     * @covers ::parseRuleWithAdditions
     * @covers ::parseSchema
     * @covers ::parseSchemas
     * @covers ::parseTupleSchemas
     *
     * @return void
     */
    public function testParse(): void
    {
        $ruleFactory = new RuleFactory();
        $parser = new Parser($ruleFactory);

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
            ],
        ]);

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
                ]),
            $schema
        );
    }
}

