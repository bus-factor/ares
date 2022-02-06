<?php

declare(strict_types=1);

/**
 * RequiredRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;
use Ares\Validation\Context;

/**
 * Class RequiredRule
 */
class RequiredRule extends AbstractRule
{
    public const ID = 'required';
    public const ERROR_MESSAGE = 'Value required';

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return Type::getValidValues();
    }

    /**
     * @param Context $context Validation context.
     * @return bool
     */
    public function isApplicable(Context $context): bool
    {
        return true;
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
        if (!is_bool($args)) {
            throw new InvalidValidationRuleArgsException(
                'Invalid args: ' . json_encode($args)
            );
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
            $message = $this->getErrorMessage(
                $context,
                self::ID,
                self::ERROR_MESSAGE
            );

            $context->addError(
                self::ID,
                $context->getErrorMessageRenderer()->render(
                    $context,
                    self::ID,
                    $message
                )
            );
        }

        return false;
    }
}
