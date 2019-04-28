<?php

declare(strict_types=1);

/**
 * string_values.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-27
 */

use Ares\Ares;

$schema = [
    'type' => 'map',
    'schema' => [
        'list' => [
            'type' => 'list',
            'schema' => [
                'type' => 'string'
            ],
        ],
        'tuple' => [
            'type' => 'tuple',
            'schema' => [
                ['type' => 'string'],
                ['type' => 'string'],
            ],
        ],
        'string_3' => ['type' => 'string'],
        'string_4' => ['type' => 'string'],
        'string_5' => ['type' => 'string'],
        'string_6' => ['type' => 'string'],
    ],
];

$data = [
    'list' => ['2', '42', [], ['foo' => 'bar'], 'abc'],
    'tuple' => [false, true],
    'string_5' => '2.55',
    'string_6' => 13.37,
];

$dataExpected = [
    'list' => ['2', '42', [], ['foo' => 'bar'], 'abc'],
    'tuple' => [false, true],
    'string_5' => '2.55',
    'string_6' => 13.37,
];

$ares = new Ares($schema);

$dataSanitized = $ares->sanitize($data);

$this->assertEquals($dataExpected, $dataSanitized);
