<?php

declare(strict_types=1);

/**
 * non_required_blankable_string.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-08
 */

use Ares\Validator;

$schema = ['type' => 'string', 'required' => false, 'blankable' => true];
$data = " \n\t\r ";

$expectedErrors = [];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

