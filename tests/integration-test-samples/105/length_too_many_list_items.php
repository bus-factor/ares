<?php

declare(strict_types=1);

/**
 * length_too_many_list_items.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-20
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'list',
    'length' => 4,
    'schema' => [
        'type' => 'integer',
    ],
];

$data = [1, 2, 3, 4, 5];

$expectedErrors = [
    new Error([''], 'length', 'Invalid value length'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

