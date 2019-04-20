<?php

declare(strict_types=1);

/**
 * string_containing_only_whitespace.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-08
 */

use Ares\Error\Error;
use Ares\Validator;

$schema = ['type' => 'string'];
$data = " \n\t\r ";

$expectedErrors = [
    new Error([''], 'blankable', 'Value must not be blank'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

