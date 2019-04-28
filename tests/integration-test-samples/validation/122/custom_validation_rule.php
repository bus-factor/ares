<?php

declare(strict_types=1);

/**
 * custom_validation_rule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-28
 */

use Ares\Ares;
use Ares\Validation\Context;
use Ares\Validation\Error\Error;
use Ares\Validation\RuleRegistry;
use Ares\Validation\Rule\RuleInterface;

$ruleId = 'myRule';

$rule = new class implements RuleInterface {
    public function validate($config, $data, Context $context): bool {
        $context->addError('myRule', 'Working');

        return false;
    }
};

RuleRegistry::register($ruleId, $rule);

$schema = [
    'type' => 'string',
    $ruleId => true,
];

$data = 'will fail in any case';

$expectedErrors = [
    new Error([''], $ruleId, 'Working'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());
