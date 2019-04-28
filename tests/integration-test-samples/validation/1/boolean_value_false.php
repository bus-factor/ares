<?php

declare(strict_types=1);

/**
 * boolean_value_false.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

use Ares\Ares;

$schema = ['type' => 'boolean'];
$data = false;

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

