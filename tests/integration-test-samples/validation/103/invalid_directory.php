<?php

declare(strict_types=1);

/**
 * invalid_directory.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-17
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = ['type' => 'string', 'directory' => true];
$data = __DIR__ . uniqid();

$expectedErrors = [
    new Error([''], 'directory', 'Directory not found'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

