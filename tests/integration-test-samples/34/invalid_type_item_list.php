<?php

declare(strict_types=1);

/**
 * invalid_type_item_list.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-31
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'list',
    'schema' => [
        'type' => 'integer',
    ],
];

$data = ['foo', 'bar'];

$expectedErrors = [
    new Error(['', '0'], 'type', 'Invalid type'),
    new Error(['', '1'], 'type', 'Invalid type'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

