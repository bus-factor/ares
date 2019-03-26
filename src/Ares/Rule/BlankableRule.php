<?php

declare(strict_types=1);

/**
 * BlankableRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InvalidValidationRuleArgsException;

/**
 * Class BlankableRule
 */
class BlankableRule implements RuleInterface
{
    const ID            = 'blankable';
    const ERROR_MESSAGE = 'Value must not be blank';

    /**
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function validate($args, $data, Context $context): bool
    {
        if (!is_bool($args) && !is_array($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if ($args === true || !is_string($data) || trim($data) != '') {
            return true;
        }

        $source = $context->getSource();
        $field = end($source);

        $errorMessageFormat = $args['message'] ?? self::ERROR_MESSAGE;

        $errorMessage = $context->getErrorMessageRenderer()
            ->render($context, self::ID, $errorMessageFormat, ['field' => $field]);

        $context->addError(
            self::ID,
            $errorMessage
        );

        return false;
    }
}

