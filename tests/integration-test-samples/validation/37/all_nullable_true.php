<?php

declare(strict_types=1);

/**
 * all_nullable_true.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-15
 */

use Ares\Ares;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [
            'type' => 'string',
        ],
    ],
];

$options = [
    'allNullable' => true,
];

$data = ['name' => null];

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data, $options));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

