<?php

declare(strict_types=1);

/**
 * valid_tuple.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-18
 */

use Ares\Validation\Validator;

$schema = [
    'type' => 'tuple',
    'schema' => [
        ['type' => 'integer'],
        ['type' => 'string'],
    ],
];

$data = [1, 'foobar'];

$expectedErrors = [];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

