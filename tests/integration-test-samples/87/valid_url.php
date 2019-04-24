<?php

declare(strict_types=1);

/**
 * valid_url.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

use Ares\Validation\Validator;

$schema = [
    'type' => 'string',
    'url' => true,
];

$data = 'https://example.com';

$expectedErrors = [];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

