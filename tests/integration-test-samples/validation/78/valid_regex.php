<?php

declare(strict_types=1);

/**
 * valid_regex.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

use Ares\Ares;

$schema = [
    'type' => 'string',
    'regex' => '/^foo$/',
];

$data = 'foo';

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

