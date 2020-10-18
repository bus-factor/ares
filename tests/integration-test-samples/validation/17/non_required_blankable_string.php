<?php

declare(strict_types=1);

/**
 * non_required_blankable_string.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-08
 */

use Ares\Ares;

$schema = ['type' => 'string', 'blankable' => true];
$data = " \n\t\r ";

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

