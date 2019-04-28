<?php

declare(strict_types=1);

/**
 * multiple_uses_of_recursive_custom_type.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-28
 */

use Ares\Ares;
use Ares\Schema\TypeRegistry;

TypeRegistry::register('Person', [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
        'mother' => ['type' => 'Person'],
        'father' => ['type' => 'Person'],
    ],
    'required' => false,
]);

$schema = [
    'type' => 'Person',
];

$data = [
    'name' => 'James Doe',
    'mother' => ['name' => 'Jane Doe'],
    'father' => ['name' => 'John Doe'],
];

$ares = new Ares($schema);

$this->assertTrue($ares->validate($data));
$this->assertCount(0, $ares->getValidationErrors());

