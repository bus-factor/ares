<?php

declare(strict_types=1);

/**
 * string_required_but_not_blankable.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-08
 */

use Ares\Validation\Error;
use Ares\Validation\Validator;

$schema = ['type' => 'string', 'required' => true];
$data = " \n\t\r ";

$expectedErrors = [
    new Error([''], 'blankable', 'Value must not be blank'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

