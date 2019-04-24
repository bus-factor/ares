<?php

declare(strict_types=1);

/**
 * invalid_regex.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'string',
    'regex' => '/^foo$/',
];

$data = 'foobar';

$expectedErrors = [
    new Error([''], 'regex', 'Value invalid'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

