<?php

declare(strict_types=1);

/**
 * non_map_value_integer.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Error\Error;
use Ares\Validator;

$schema = ['type' => 'map'];
$data = 42;

$expectedErrors = [
    new Error([''], 'type', 'Invalid type'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

