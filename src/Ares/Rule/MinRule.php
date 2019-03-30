<?php

declare(strict_types=1);

/**
 * MinRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InapplicableValidationRuleException;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\TypeRule;
use Ares\Schema\Type;

/**
 * Class MinRule
 */
class MinRule implements RuleInterface
{
    const ID            = 'min';
    const ERROR_MESSAGE = 'Value must not be smaller than {value}';

    /**
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InapplicableValidationRuleException
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function validate($args, $data, Context $context): bool
    {
        $schema = $context->getSchema();

        if (!in_array($schema[TypeRule::ID], [Type::FLOAT, Type::INTEGER], true)) {
            throw new InapplicableValidationRuleException('This rule applies to <float> and <integer> types only');
        }

        if (!is_numeric($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if ($data >= $args) {
            return true;
        }

        $context->addError(
            self::ID,
            $context->getErrorMessageRenderer()
                ->render($context, self::ID, self::ERROR_MESSAGE, ['value' => $args])
        );

        return false;
    }
}

