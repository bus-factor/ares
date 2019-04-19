<?php

declare(strict_types=1);

/**
 * UnknownRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-21
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Schema;
use Ares\Schema\Type;

/**
 * Class UnknownRule
 */
class UnknownRule extends AbstractRule
{
    public const ID            = 'unknown';
    public const ERROR_MESSAGE = 'Unknown field';

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return [
            Type::MAP,
        ];
    }

    /**
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function performValidation($args, $data, Context $context): bool
    {
        if ($args) {
            return true;
        }

        $schema = $context->getSchema();

        $unknownFields = array_diff_key($data, $schema->getSchemas());

        foreach ($unknownFields as $field => $value) {
            $context->enter($field, new Schema());

            $context->addError(
                self::ID,
                $context->getErrorMessageRenderer()->render($context, self::ID, self::ERROR_MESSAGE)
            );

            $context->leave();
        }

        return true;
    }
}

