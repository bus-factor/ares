<?php

declare(strict_types=1);

/**
 * all_nullable_false_with_override.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-15
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [
            'type' => 'string',
        ],
        'email' => [
            'type' => 'string',
            'nullable' => true,
        ],
    ],
];

$options = [
    'allNullable' => false,
];

$data = ['name' => null, 'email' => null];

$expectedErrors = [
    new Error(['', 'name'], 'nullable', 'Value must not be null'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data, $options));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

