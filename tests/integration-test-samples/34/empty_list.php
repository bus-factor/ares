<?php

declare(strict_types=1);

/**
 * empty_list.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-31
 */

use Ares\Validation\Validator;

$schema = [
    'type' => 'list',
    'schema' => [
        'type' => 'integer',
    ],
];

$data = [];

$expectedErrors = [];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

