<?php

declare(strict_types=1);

/**
 * float_values.php
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
                'type' => 'float'
            ],
        ],
        'tuple' => [
            'type' => 'tuple',
            'schema' => [
                ['type' => 'float'],
                ['type' => 'float'],
            ],
        ],
        'float_3' => ['type' => 'float'],
        'float_4' => ['type' => 'float'],
        'float_5' => ['type' => 'float'],
        'float_6' => ['type' => 'float'],
        'float_7' => ['type' => 'float'],
    ],
];

$data = [
    'list' => ['2', '42', [], ['foo' => 'bar'], 'abc'],
    'tuple' => [false, true],
    'float_5' => '2.55',
    'float_6' => 13.37,
    'float_7' => 13,
];

$dataExpected = [
    'list' => [2.0, 42.0, [], ['foo' => 'bar'], 'abc'],
    'tuple' => [false, true],
    'float_5' => 2.55,
    'float_6' => 13.37,
    'float_7' => 13.0,
];

$ares = new Ares($schema);

$dataSanitized = $ares->sanitize($data);

$this->assertEquals($dataExpected, $dataSanitized);
$this->assertSame(2.0, $dataSanitized['list'][0]);
$this->assertSame(42.0, $dataSanitized['list'][1]);
$this->assertSame(false, $dataSanitized['tuple'][0]);
$this->assertSame(true, $dataSanitized['tuple'][1]);
$this->assertSame(2.55, $dataSanitized['float_5']);
$this->assertSame(13.37, $dataSanitized['float_6']);
$this->assertSame(13.0, $dataSanitized['float_7']);

