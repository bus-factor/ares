<?php

declare(strict_types=1);

/**
 * boolean_values.php
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
                'type' => 'boolean'
            ],
        ],
        'tuple' => [
            'type' => 'tuple',
            'schema' => [
                ['type' => 'boolean'],
                ['type' => 'boolean'],
            ],
        ],
        'boolean_3' => ['type' => 'boolean'],
        'boolean_4' => ['type' => 'boolean'],
        'boolean_5' => ['type' => 'boolean'],
        'boolean_6' => ['type' => 'boolean'],
    ],
];

$data = [
    'list' => ['0', '1', [], ['foo' => 'bar']],
    'tuple' => [0, 1],
    'boolean_5' => false,
    'boolean_6' => true,
];

$dataExpected = [
    'list' => [false, true, [], ['foo' => 'bar']],
    'tuple' => [false, true],
    'boolean_5' => false,
    'boolean_6' => true,
];

$ares = new Ares($schema);

$dataSanitized = $ares->sanitize($data);

$this->assertEquals($dataExpected, $dataSanitized);
