<?php

declare(strict_types=1);

/**
 * valid_file.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-17
 */

use Ares\Validation\Validator;

$schema = ['type' => 'string', 'file' => true];
$data = __FILE__;

$expectedErrors = [];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

