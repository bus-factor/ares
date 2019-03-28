<?php

declare(strict_types=1);

/**
 * valid_email_address.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

use Ares\Validator;

$schema = [
    'type' => 'string',
    'email' => true,
];

$data = 'john.doe@example.com';

$expectedErrors = [];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

