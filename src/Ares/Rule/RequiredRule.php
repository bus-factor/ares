<?php

declare(strict_types=1);

/**
 * RequiredRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;

/**
 * Class RequiredRule
 */
class RequiredRule extends AbstractRule
{
    const ID = 'required';
    const ERROR_MESSAGE = 'Value required';

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return Type::getValues();
    }

    /**
     * @param \Ares\Context $context Validation context.
     * @return bool
     */
    public function isApplicable(Context $context): bool
    {
        return true;
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
        if (!is_bool($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        $references = $context->getSource();
        $field = array_pop($references);

        if (empty($references)) {
            return true;
        }

        array_shift($references);

        $ptr = &$context->getData();

        foreach ($references as $reference) {
            $ptr = &$ptr[$reference];
        }

        if (array_key_exists($field, $ptr)) {
            return true;
        }

        if ($args) {
            $message = $context->getSchema()->hasRule(self::ID)
                ? ($context->getSchema()->getRule(self::ID)->getMessage() ?? self::ERROR_MESSAGE)
                : self::ERROR_MESSAGE;

            $context->addError(
                self::ID,
                $context->getErrorMessageRenderer()->render($context, self::ID, $message)
            );
        }

        return false;
    }
}

