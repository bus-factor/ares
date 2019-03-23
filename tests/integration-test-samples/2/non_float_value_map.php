<?php

declare(strict_types=1);

/**
 * non_float_value_map.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Error;
use Ares\Validator;

$schema = ['type' => 'float'];
$data = ['foo' => 'bar'];

$expectedErrors = [
    new Error([''], 'type', 'Invalid type'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

