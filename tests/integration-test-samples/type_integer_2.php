<?php

declare(strict_types=1);

/**
 * type_integer_2.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

use Ares\Validation\Error;
use Ares\Validation\Validator;

$schema = ['type' => 'integer'];
$data = 42.00;

$expectedErrors = [
    new Error([''], 'type', 'Invalid type'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

