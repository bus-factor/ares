<?php

declare(strict_types=1);

/**
 * custom_decimal_type.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-27
 */

use Ares\Ares;
use Ares\Schema\TypeRegistry;
use Ares\Validation\Error\Error;

TypeRegistry::register('decimal', [
    'type' => 'string',
    ['regex' => '/^(0|[1-9]\d*)\.\d{2}$/', 'message' => 'Invalid decimal value (e.g. "3.55")'],
]);

$schema = [
    'type' => 'decimal',
];

$data = '1.6';

$expectedErrors = [
    new Error([''], 'regex', 'Invalid decimal value (e.g. "3.55")'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

