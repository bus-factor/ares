<?php

declare(strict_types=1);

/**
 * UnknownRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-21
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Validation\Context;
use Ares\Validation\Schema\Type;

/**
 * Class UnknownRule
 */
class UnknownRule implements RuleInterface
{
    const ID            = 'unknown';
    const ERROR_MESSAGE = 'Unknown field';

    /**
     * @param mixed                    $args    Validation rule configuration.
     * @param mixed                    $data    Input data.
     * @param \Ares\Validation\Context $context Validation context.
     * @return boolean
     */
    public function validate($args, $data, Context $context): bool
    {
        if ($args) {
            return true;
        }

        $schema = $context->getSchema();

        if ($schema[TypeRule::ID] !== Type::MAP || !is_array($data)) {
            return true;
        }

        $unknownFields = array_diff_key($data, $schema['schema']);

        foreach ($unknownFields as $field => $value) {
            $context->enter($field, []);
            $context->addError(self::ID, self::ERROR_MESSAGE);
            $context->leave();
        }

        return true;
    }
}

