<?php

declare(strict_types=1);

/**
 * invalid_regex.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'string',
    'regex' => '/^foo$/',
];

$data = 'foobar';

$expectedErrors = [
    new Error([''], 'regex', 'Value invalid'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

