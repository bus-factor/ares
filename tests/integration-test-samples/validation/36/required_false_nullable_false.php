<?php

declare(strict_types=1);

/**
 * required_false_nullable_false.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-13
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [
            'type' => 'string',
            'required' => false,
            'nullable' => false,
        ],
    ],
];

$data = ['name' => null];

$expectedErrors = [
    new Error(['', 'name'], 'nullable', 'Value must not be null'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

