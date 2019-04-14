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
class UnknownRule implements RuleInterface
{
    const ID            = 'unknown';
    const ERROR_MESSAGE = 'Unknown field';

    /**
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
     * @return boolean
     */
    public function validate($args, $data, Context $context): bool
    {
        if ($args) {
            return true;
        }

        $schema = $context->getSchema();

        if ($schema->getRule(TypeRule::ID)->getArgs() !== Type::MAP || !is_array($data)) {
            return true;
        }

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

