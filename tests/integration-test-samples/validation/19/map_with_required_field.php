<?php

declare(strict_types=1);

/**
 * map_with_required_field.php
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
        'email' => ['type' => 'string', 'required' => false],
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

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

