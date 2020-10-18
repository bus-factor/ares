<?php

declare(strict_types=1);

/**
 * cyclic_custom_type_references.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-28
 */

use Ares\Ares;
use Ares\Schema\TypeRegistry;
use Ares\Validation\Error\Error;

TypeRegistry::register('Person', [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
        'age' => ['type' => 'integer'],
        'friends' => [
            'type' => 'list',
            'schema' => [
                'type' => 'Person',
            ],
            'required' => false,
        ],
    ],
]);

$schema = [
    'type' => 'Person',
];

$data = [
    'name' => 'John Doe',
    'friends' => [
        [
            'name' => 'Frank Woods',
            'age' => 23,
            'friends' => [
                ['name' => 'John Jackson'],
            ],
        ],
        ['name' => 'Jack Johnson'],
    ],
];

$expectedErrors = [
    new Error(['', 'age'], 'required', 'Value required'),
    new Error(['', 'friends', 0, 'friends', 0, 'age'], 'required', 'Value required'),
    new Error(['', 'friends', 1, 'age'], 'required', 'Value required'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

