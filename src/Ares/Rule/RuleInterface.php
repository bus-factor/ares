<?php

declare(strict_types=1);

/**
 * RuleInterface.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-26
 */

namespace Ares\Rule;

use Ares\Context;

/**
 * Interface RuleInterface
 */
interface RuleInterface
{
    /**
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function validate($args, $data, Context $context): bool;
}

