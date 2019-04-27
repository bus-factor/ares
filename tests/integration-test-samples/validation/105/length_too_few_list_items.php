<?php

declare(strict_types=1);

/**
 * length_too_few_list_items.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-20
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'list',
    'length' => 4,
    'schema' => [
        'type' => 'integer',
    ],
];

$data = [1, 2, 3];

$expectedErrors = [
    new Error([''], 'length', 'Invalid value length'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

