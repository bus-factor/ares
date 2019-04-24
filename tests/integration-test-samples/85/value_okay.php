<?php

declare(strict_types=1);

/**
 * value_okay.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

use Ares\Validation\Validator;

$schema = [
    'type' => 'integer',
    'max' => 42,
];

$data = 41;

$expectedErrors = [];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

