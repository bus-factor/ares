<?php

declare(strict_types=1);

/**
 * trim_strings_disabled.php
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
    'list' => ['   2', '42  ', [], ['foo' => 'bar'], '  abc  '],
    'tuple' => [false, true],
    'string_3' => "\n\n2.55\tbla\t",
];

$dataExpected = [
    'list' => ['   2', '42  ', [], ['foo' => 'bar'], '  abc  '],
    'tuple' => [false, true],
    'string_3' => "\n\n2.55\tbla\t",
];

$ares = new Ares($schema);

$dataSanitized = $ares->sanitize($data, ['trimStrings' => false]);

$this->assertEquals($dataExpected, $dataSanitized);
