<?php

declare(strict_types=1);

/**
 * map_without_schema.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Validator;

$schema = [
    'type' => 'map',
    'required' => true,
];

$data = [];

$validator = new Validator($schema);

$this->assertTrue($validator->validate($data));

