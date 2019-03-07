<?php

declare(strict_types=1);

/**
 * boolean_value_required.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

use Ares\Validation\Error;
use Ares\Validation\Validator;

$schema = ['type' => 'boolean', 'required' => true];
$data = null;

$expectedErrors = [
    new Error([''], 'required', 'Value required'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

