<?php

declare(strict_types=1);

/**
 * EmailRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;
use Ares\Validation\Context;

/**
 * Class EmailRule
 */
class EmailRule extends AbstractRule
{
    public const ID            = 'email';
    public const ERROR_MESSAGE = 'Invalid email address';

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return [
            Type::STRING,
        ];
    }

    /**
     * @param mixed   $args    Validation rule configuration.
     * @param mixed   $data    Input data.
     * @param Context $context Validation context.
     * @return boolean
     * @throws InvalidValidationRuleArgsException
     */
    public function performValidation($args, $data, Context $context): bool
    {
        if (!is_bool($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if (!$args) {
            return true;
        }

        if (!is_string($data)) {
            $message = $context->getSchema()->getRule(self::ID)->getMessage() ?? self::ERROR_MESSAGE;

            $context->addError(
                self::ID,
                $context->getErrorMessageRenderer()->render($context, self::ID, $message)
            );

            return false;
        }

        $email = filter_var($data, FILTER_VALIDATE_EMAIL);

        if ($email === false) {
            $message = $context->getSchema()->getRule(self::ID)->getMessage() ?? self::ERROR_MESSAGE;

            $context->addError(
                self::ID,
                $context->getErrorMessageRenderer()->render($context, self::ID, $message)
            );

            return false;
        }


        return true;
    }
}

