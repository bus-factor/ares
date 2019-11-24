<?php

declare(strict_types=1);

/**
 * UuidRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-11-24
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;
use Ares\Validation\Context;

/**
 * Class UuidRule
 */
class UuidRule extends AbstractRule
{
    public const ID            = 'uuid';
    public const ERROR_MESSAGE = 'Invalid UUID';

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
            $message = $context->getSchema()->hasRule(self::ID)
                ? ($context->getSchema()->getRule(self::ID)->getMessage() ?? self::ERROR_MESSAGE)
                : self::ERROR_MESSAGE;

            $context->addError(
                self::ID,
                $context->getErrorMessageRenderer()->render($context, self::ID, $message)
            );

            return false;
        }

        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $data) !== 1) {
            $message = $context->getSchema()->hasRule(self::ID)
                ? ($context->getSchema()->getRule(self::ID)->getMessage() ?? self::ERROR_MESSAGE)
                : self::ERROR_MESSAGE;

            $context->addError(
                self::ID,
                $context->getErrorMessageRenderer()->render($context, self::ID, $message)
            );

            return false;
        }

        return true;
    }
}

