<?php

declare(strict_types=1);

/**
 * maxlength_too_many_list_items.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-20
 */

use Ares\Error\Error;
use Ares\Validator;

$schema = [
    'type' => 'list',
    'maxlength' => 3,
    'schema' => [
        'type' => 'integer',
    ],
];

$data = [1, 2, 3, 4];

$expectedErrors = [
    new Error([''], 'maxlength', 'Value too long'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

