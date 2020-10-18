<?php

declare(strict_types=1);

/**
 * valid_email_address.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

use Ares\Ares;

$schema = [
    'type' => 'string',
    'email' => true,
];

$data = 'john.doe@example.com';

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

