<?php

declare(strict_types=1);

/**
 * invalid_numeric.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-19
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = ['type' => 'numeric'];
$data = '13.37';

$expectedErrors = [
    new Error([''], 'type', 'Invalid type'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

