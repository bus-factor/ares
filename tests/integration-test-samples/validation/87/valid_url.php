<?php

declare(strict_types=1);

/**
 * valid_url.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

use Ares\Ares;

$schema = [
    'type' => 'string',
    'url' => true,
];

$data = 'https://example.com';

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

