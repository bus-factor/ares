<?php

declare(strict_types=1);

/**
 * valid_string_length.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

use Ares\Ares;

$schema = [
    'type' => 'string',
    'minlength' => 3,
];

$data = 'John Doe';

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

