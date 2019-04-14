<?php

declare(strict_types=1);

/**
 * MaxRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InapplicableValidationRuleException;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\TypeRule;
use Ares\Schema\Type;

/**
 * Class MaxRule
 */
class MaxRule implements RuleInterface
{
    const ID            = 'max';
    const ERROR_MESSAGE = 'Value must not be greater than {value}';

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

        if (!in_array($schema->getRule(TypeRule::ID)->getArgs(), [Type::FLOAT, Type::INTEGER], true)) {
            throw new InapplicableValidationRuleException('This rule applies to <float> and <integer> types only');
        }

        if (!is_numeric($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if ($data <= $args) {
            return true;
        }

        $message = $context->getSchema()->getRule(self::ID)->getMessage() ?? self::ERROR_MESSAGE;

        $context->addError(
            self::ID,
            $context->getErrorMessageRenderer()
                ->render($context, self::ID, $message, ['value' => $args])
        );

        return false;
    }
}

