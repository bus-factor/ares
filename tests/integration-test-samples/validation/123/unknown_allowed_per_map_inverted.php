<?php

declare(strict_types=1);

/**
 * unknown_allowed_per_map_inverted.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-21
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
        'meta' => [
            'type' => 'map',
            'schema' => [
            ],
            'unknownAllowed' => false,
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
    new Error(['', 'meta', 'hobbies'], 'unknownAllowed', 'Unknown field'),
    new Error(['', 'meta', 'occupation'], 'unknownAllowed', 'Unknown field'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data, ['allUnknownAllowed' => true]));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

