<?php

declare(strict_types=1);

/**
 * EmailRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InvalidValidationRuleArgsException;

/**
 * Class EmailRule
 */
class EmailRule implements RuleInterface
{
    const ID            = 'email';
    const ERROR_MESSAGE = 'Invalid email address';

    /**
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function validate($args, $data, Context $context): bool
    {
        if (!is_bool($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if (!$args) {
            return true;
        }

        if (!is_string($data)) {
            $context->addError(
                self::ID,
                $context->getErrorMessageRenderer()->render($context, self::ID, self::ERROR_MESSAGE)
            );

            return false;
        }

        $email = filter_var($data, FILTER_VALIDATE_EMAIL);

        if ($email === false) {
            $context->addError(
                self::ID,
                $context->getErrorMessageRenderer()->render($context, self::ID, self::ERROR_MESSAGE)
            );

            return false;
        }


        return true;
    }
}
