<?php

declare(strict_types=1);

/**
 * RuleInterface.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace Ares\Validation\Rule;

use Ares\Validation\Context;

/**
 * Interface RuleInterface
 */
interface RuleInterface
{
    /**
     * @param mixed                    $config  Validation rule configuration.
     * @param mixed                    $data    Input data.
     * @param \Ares\Validation\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function validate($config, $data, Context $context): bool;
}

