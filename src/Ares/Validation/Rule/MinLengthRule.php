<?php

declare(strict_types=1);

/**
 * MinLengthRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Validation\Context;

/**
 * Class MinLengthRule
 */
class MinLengthRule implements RuleInterface
{
    const ID            = 'minlength';
    const ERROR_MESSAGE = 'Value too short';

    /**
     * @param mixed                    $args    Validation rule configuration.
     * @param mixed                    $data    Input data.
     * @param \Ares\Validation\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function validate($args, $data, Context $context): bool
    {
        if (!is_int($args) || $args < 0) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if (!is_string($data)) {
            return true;
        }

        if (strlen($data) >= $args) {
            return true;
        }

        $context->addError(self::ID, self::ERROR_MESSAGE);

        return false;
    }
}

