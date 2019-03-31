<?php

declare(strict_types=1);

/**
 * RegexRule.php
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
 * Class RegexRule
 */
class RegexRule implements RuleInterface
{
    const ID            = 'regex';
    const ERROR_MESSAGE = 'Value invalid';

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

        if ($schema[TypeRule::ID] !== Type::STRING) {
            throw new InapplicableValidationRuleException('This rule applies to <string> types only');
        }

        if (!is_string($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if (preg_match($args, $data) === 1) {
            return true;
        }

        $context->addError(
            self::ID,
            $context->getErrorMessageRenderer()->render($context, self::ID, self::ERROR_MESSAGE)
        );

        return false;
    }
}

