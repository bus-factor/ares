<?php

declare(strict_types=1);

/**
 * UnknownAllowedRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-21
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use Ares\Validation\Context;

/**
 * Class UnknownAllowedRule
 */
class UnknownAllowedRule extends AbstractRule
{
    public const ID            = 'unknownAllowed';
    public const ERROR_MESSAGE = 'Unknown field';

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return [
            Type::MAP,
            Type::TUPLE,
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
        if ($args) {
            return true;
        }

        $schema = $context->getSchema();
        $unknownFields = array_keys(array_diff_key($data, $schema->getSchemas()));

        $message = $schema->hasRule(self::ID)
            ? ($schema->getRule(self::ID)->getMessage() ?? self::ERROR_MESSAGE)
            : self::ERROR_MESSAGE;

        foreach ($unknownFields as $field) {
            $context->enter($field, new Schema());

            $context->addError(
                self::ID,
                $context->getErrorMessageRenderer()->render($context, self::ID, $message)
            );

            $context->leave();
        }

        return true;
    }
}

