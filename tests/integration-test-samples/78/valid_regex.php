<?php

declare(strict_types=1);

/**
 * valid_regex.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

use Ares\Validation\Validator;

$schema = [
    'type' => 'string',
    'regex' => '/^foo$/',
];

$data = 'foo';

$expectedErrors = [];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

