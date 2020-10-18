<?php

declare(strict_types=1);

/**
 * unknown_map_fields.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

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
    new Error(['', 'hobby'], 'unknownAllowed', 'Unknown field'),
    new Error(['', 'meta', 'size'], 'unknownAllowed', 'Unknown field'),
    new Error(['', 'meta', 'joined'], 'unknownAllowed', 'Unknown field'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

