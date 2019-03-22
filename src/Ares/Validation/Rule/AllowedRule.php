<?php

declare(strict_types=1);

/**
 * AllowedRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-21
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Validation\Context;

/**
 * Class AllowedRule
 */
class AllowedRule implements RuleInterface
{
    const ID            = 'allowed';
    const ERROR_MESSAGE = 'Value not allowed';

    /**
     * @param mixed                    $args    Validation rule configuration.
     * @param mixed                    $data    Input data.
     * @param \Ares\Validation\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function validate($args, $data, Context $context): bool
    {
        if (!is_array($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if (in_array($data, $args, true)) {
            return true;
        }

        $context->addError(self::ID, self::ERROR_MESSAGE);

        return false;
    }
}

