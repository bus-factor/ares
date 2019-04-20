<?php

declare(strict_types=1);

/**
 * too_many_tuple_items.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-20
 */

use Ares\Error\Error;
use Ares\Validator;

$schema = [
    'type' => 'tuple',
    'schema' => [
        ['type' => 'string'],
        ['type' => 'integer'],
    ],
];

$data = ['foo', 1337, 'bar'];

$expectedErrors = [
    new Error(['', 2], 'unknown', 'Unknown field'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

