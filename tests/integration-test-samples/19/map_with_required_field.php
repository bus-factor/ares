<?php

declare(strict_types=1);

/**
 * map_with_required_field.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Error\Error;
use Ares\Validator;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string', 'required' => true],
        'email' => ['type' => 'string'],
        'phone' => ['type' => 'string', 'required' => true],
        'meta' => [
            'type' => 'map',
            'required' => false,
            'schema' => [
                'age' => ['type' => 'integer', 'required' => true],
                'size' => ['type' => 'float', 'required' => true],
            ],
        ],
    ],
];

$data = [
    'name' => 'John Doe',
    'meta' => [
        'size' => 1.80,
    ],
];

$expectedErrors = [
    new Error(['', 'phone'], 'required', 'Value required'),
    new Error(['', 'meta', 'age'], 'required', 'Value required'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

