<?php

declare(strict_types=1);

/**
 * ForbiddenRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-22
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Validation\Context;

/**
 * Class ForbiddenRule
 */
class ForbiddenRule implements RuleInterface
{
    const ID            = 'forbidden';
    const ERROR_MESSAGE = 'Value forbidden';

    /**
     * @param mixed                    $args    Validation rule configuration.
     * @param mixed                    $data    Input data.
     * @param \Ares\Validation\Context $context Validation context.
     * @return boolean
     */
    public function validate($args, $data, Context $context): bool
    {
        if (!is_array($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if (!in_array($data, $args, true)) {
            return true;
        }

        $context->addError(self::ID, self::ERROR_MESSAGE);

        return false;
    }
}

