<?php

declare(strict_types=1);

/**
 * string_sorrounded_by_whitespace.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-08
 */

use Ares\Ares;

$schema = ['type' => 'string'];
$data = " foobar \n";

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

