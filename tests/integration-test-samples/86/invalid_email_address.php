<?php

declare(strict_types=1);

/**
 * invalid_email_address.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'string',
    'email' => true,
];

$data = 'foo';

$expectedErrors = [
    new Error([''], 'email', 'Invalid email address'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

