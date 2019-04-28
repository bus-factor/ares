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
use Ares\Schema\Type;
use Ares\Validation\Context;

/**
 * Class ForbiddenRule
 */
class ForbiddenRule extends AbstractRule
{
    public const ID            = 'forbidden';
    public const ERROR_MESSAGE = 'Value forbidden';

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return [
            Type::BOOLEAN,
            Type::FLOAT,
            Type::INTEGER,
            Type::NUMERIC,
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
        if (!is_array($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if (!in_array($data, $args, true)) {
            return true;
        }

        $message = $context->getSchema()->getRule(self::ID)->getMessage() ?? self::ERROR_MESSAGE;

        $context->addError(
            self::ID,
            $context->getErrorMessageRenderer()->render($context, self::ID, $message)
        );

        return false;
    }
}

