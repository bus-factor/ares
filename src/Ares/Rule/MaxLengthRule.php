<?php

declare(strict_types=1);

/**
 * MaxLengthRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InvalidValidationRuleArgsException;

/**
 * Class MaxLengthRule
 */
class MaxLengthRule implements RuleInterface
{
    const ID            = 'maxlength';
    const ERROR_MESSAGE = 'Value too long';

    /**
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
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

        if (strlen($data) <= $args) {
            return true;
        }

        $context->addError(self::ID, self::ERROR_MESSAGE);

        return false;
    }
}

