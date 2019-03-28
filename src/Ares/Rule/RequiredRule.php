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

/**
 * Class RequiredRule
 */
class RequiredRule implements RuleInterface
{
    const ID = 'required';
    const ERROR_MESSAGE = 'Value required';

    /**
     * Validates the presence of a required data field.
     *
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function validate($args, $data, Context $context): bool
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
            $errorMessage = $context->getErrorMessageRenderer()
                ->render($context, self::ID, self::ERROR_MESSAGE, ['field' => $field]);

            $context->addError(
                self::ID,
                $errorMessage
            );
        }

        return false;
    }
}

