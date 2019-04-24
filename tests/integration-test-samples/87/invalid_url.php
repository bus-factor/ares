<?php

declare(strict_types=1);

/**
 * invalid_url.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'string',
    'url' => true,
];

$data = 'foo';

$expectedErrors = [
    new Error([''], 'url', 'Invalid URL'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

