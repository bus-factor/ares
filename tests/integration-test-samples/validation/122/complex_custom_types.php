<?php

declare(strict_types=1);

/**
 * complex_custom_types.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-27
 */

use Ares\Ares;
use Ares\Schema\TypeRegistry;
use Ares\Validation\Error\Error;

TypeRegistry::register('Id', [
    'type' => 'integer',
    'min' => 1,
]);

TypeRegistry::register('Date', [
    'type' => 'string',
    'datetime' => 'Y-m-d',
]);

TypeRegistry::register('Email', [
    'type' => 'string',
    'email' => true,
]);

TypeRegistry::register('Post', [
    'type' => 'map',
    'schema' => [
        'author_id' => ['type' => 'Id'],
        'title' => ['type' => 'string'],
        'contents' => ['type' => 'string'],
        'created_at' => ['type' => 'Date'],
    ],
]);

TypeRegistry::register('User', [
    'type' => 'map',
    'schema' => [
        'id' => ['type' => 'Id'],
        'email' => ['type' => 'Email'],
        'posts' => [
            'type' => 'list',
            'schema' => [
                'type' => 'Post',
            ],
        ],
        'created_at' => ['type' => 'Date'],
    ],
]);

$schema = ['type' => 'User'];
$ares = new Ares($schema);

// valid data

$data = [
    'id' => 42,
    'email' => 'john.doe@example.com',
    'created_at' => '2019-04-27',
    'posts' => [
        [
            'author_id' => 42,
            'title' => 'Everyone likes talking about PHP',
            'contents' => 'Lorem ipsum...',
            'created_at' => '2019-04-27',
        ]
    ],
];

$expectedErrors = [
];

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

// invalid data

$data = [
    'id' => 0,
    'email' => 'john.doeexample.com',
    'created_at' => '2019-04-27',
    'posts' => [
        [
            'author_id' => 42,
            'contents' => 'Lorem ipsum...',
            'created_at' => '2019-04-27',
        ]
    ],
    'name' => 'John Doe',
];

$expectedErrors = [
    new Error(['', 'name'], 'unknownAllowed', 'Unknown field'),
    new Error(['', 'id'], 'min', 'Value must not be smaller than 1'),
    new Error(['', 'email'], 'email', 'Invalid email address'),
    new Error(['', 'posts', 0, 'title'], 'required', 'Value required'),
];

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

