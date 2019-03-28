<?php

declare(strict_types=1);

/**
 * UrlRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InvalidValidationRuleArgsException;

/**
 * Class UrlRule
 */
class UrlRule implements RuleInterface
{
    const ID            = 'url';
    const ERROR_MESSAGE = 'Invalid URL';

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

        $url = filter_var($data, FILTER_VALIDATE_URL);

        if ($url === false) {
            $context->addError(
                self::ID,
                $context->getErrorMessageRenderer()->render($context, self::ID, self::ERROR_MESSAGE)
            );

            return false;
        }


        return true;
    }
}

