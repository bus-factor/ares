<?php

declare(strict_types=1);

/**
 * valid_string_length.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

use Ares\Validator;

$schema = [
    'type' => 'string',
    'minlength' => 3,
];

$data = 'John Doe';

$expectedErrors = [];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

