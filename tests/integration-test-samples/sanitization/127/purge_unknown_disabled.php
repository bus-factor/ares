<?php

declare(strict_types=1);

/**
 * purge_unknown_disabled.php
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
    ],
];

$data = [
    'list' => ['2', '42', [], ['foo' => 'bar'], '  abc  '],
    'tuple' => [false, true, 1, 2, 3],
    'string_3' => "2.55\tbla",
    'string_4' => 'foo bar',
    'string_5' => 'fizz buzz',
];

$dataExpected = [
    'list' => ['2', '42', [], ['foo' => 'bar'], 'abc'],
    'tuple' => [false, true, 1, 2, 3],
    'string_3' => "2.55\tbla",
    'string_4' => 'foo bar',
    'string_5' => 'fizz buzz',
];

$ares = new Ares($schema);

$dataSanitized = $ares->sanitize($data, ['purgeUnknown' => false]);

$this->assertEquals($dataExpected, $dataSanitized);
