<?php

declare(strict_types=1);

/**
 * invalid_email_address.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'string',
    'email' => true,
];

$data = 'foo';

$expectedErrors = [
    new Error([''], 'email', 'Invalid email address'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

