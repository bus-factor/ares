<?php

declare(strict_types=1);

/**
 * allowed_unknown_map_fields.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Ares;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string', 'required' => true],
        'meta' => [
            'type' => 'map',
            'required' => false,
            'schema' => [
                'age' => ['type' => 'integer', 'required' => true],
            ],
        ],
    ],
];

$options = [
    'allUnknownAllowed' => true,
];

$data = [
    'name' => 'John Doe',
    'hobby' => 'Reading',
    'meta' => [
        'age' => 23,
        'size' => 1.80,
        'joined' => '2019-03-09',
    ],
];

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data, $options));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

