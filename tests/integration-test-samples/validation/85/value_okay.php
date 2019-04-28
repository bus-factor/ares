<?php

declare(strict_types=1);

/**
 * value_okay.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

use Ares\Ares;

$schema = [
    'type' => 'integer',
    'max' => 42,
];

$data = 41;

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

