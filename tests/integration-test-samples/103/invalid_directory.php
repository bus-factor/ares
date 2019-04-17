<?php

declare(strict_types=1);

/**
 * invalid_directory.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-17
 */

use Ares\Error\Error;
use Ares\Validator;

$schema = ['type' => 'string', 'directory' => true];
$data = __DIR__ . uniqid();

$expectedErrors = [
    new Error([''], 'directory', 'Directory not found'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

