<?php

declare(strict_types=1);

/**
 * unknown_allowed_per_map.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-21
 */

use Ares\Error\Error;
use Ares\Validator;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
        'meta' => [
            'type' => 'map',
            'schema' => [
            ],
            'unknownAllowed' => true,
        ]
    ],
];

$data = [
    'name' => 'John Doe',
    'age' => 41,
    'meta' => [
        'hobbies' => ['reading', 'biking'],
        'occupation' => 'Engineer',
    ],
];

$expectedErrors = [
    new Error(['', 'age'], 'unknownAllowed', 'Unknown field'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());
