<?php

declare(strict_types=1);

/**
 * unknown_map_fields.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Validation\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'map',
    'required' => true,
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

$data = [
    'name' => 'John Doe',
    'hobby' => 'Reading',
    'meta' => [
        'age' => 23,
        'size' => 1.80,
        'joined' => '2019-03-09',
    ],
];

$expectedErrors = [
    new Error(['', 'hobby'], 'unknown', 'Unknown field'),
    new Error(['', 'meta', 'size'], 'unknown', 'Unknown field'),
    new Error(['', 'meta', 'joined'], 'unknown', 'Unknown field'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

