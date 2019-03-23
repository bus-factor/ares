<?php

declare(strict_types=1);

/**
 * float_value_not_required.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

use Ares\Error;
use Ares\Validator;

$schema = ['type' => 'float'];
$data = null;

$expectedErrors = [
    new Error([''], 'nullable', 'Value must not be null'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

