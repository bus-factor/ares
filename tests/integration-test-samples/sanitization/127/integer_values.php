<?php

declare(strict_types=1);

/**
 * integer_values.php
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
                'type' => 'integer'
            ],
        ],
        'tuple' => [
            'type' => 'tuple',
            'schema' => [
                ['type' => 'integer'],
                ['type' => 'integer'],
            ],
        ],
        'integer_3' => ['type' => 'integer'],
        'integer_4' => ['type' => 'integer'],
        'integer_5' => ['type' => 'integer'],
        'integer_6' => ['type' => 'integer'],
    ],
];

$data = [
    'list' => ['2', '42', [], ['foo' => 'bar'], 'abc'],
    'tuple' => [false, true],
    'integer_5' => '2.55',
    'integer_6' => 13.37,
];

$dataExpected = [
    'list' => [2, 42, [], ['foo' => 'bar'], 'abc'],
    'tuple' => [0, 1],
    'integer_5' => 2,
    'integer_6' => 13,
];

$ares = new Ares($schema);

$dataSanitized = $ares->sanitize($data);

$this->assertEquals($dataExpected, $dataSanitized);
$this->assertSame(2, $dataSanitized['list'][0]);
$this->assertSame(42, $dataSanitized['list'][1]);
$this->assertSame(false, $dataSanitized['tuple'][0]);
$this->assertSame(true, $dataSanitized['tuple'][1]);
$this->assertSame(2, $dataSanitized['integer_5']);
$this->assertSame(13, $dataSanitized['integer_6']);

