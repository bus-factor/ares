<?php

declare(strict_types=1);

/**
 * invalid_file.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-17
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = ['type' => 'string', 'file' => true];
$data = __FILE__ . uniqid();

$expectedErrors = [
    new Error([''], 'file', 'File not found'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

