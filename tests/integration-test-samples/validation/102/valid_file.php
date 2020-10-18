<?php

declare(strict_types=1);

/**
 * valid_file.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-17
 */

use Ares\Ares;

$schema = ['type' => 'string', 'file' => true];
$data = __FILE__;

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

