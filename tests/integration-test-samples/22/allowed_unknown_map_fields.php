<?php

declare(strict_types=1);

/**
 * allowed_unknown_map_fields.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Error;
use Ares\Validator;

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

$options = [
    'allowUnknown' => true,
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

$validator = new Validator($schema, $options);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

