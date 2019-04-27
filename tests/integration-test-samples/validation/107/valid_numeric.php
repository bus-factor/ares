<?php

declare(strict_types=1);

/**
 * valid_numeric.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-19
 */

use Ares\Ares;

$schema = ['type' => 'numeric'];
$data = 42;

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

