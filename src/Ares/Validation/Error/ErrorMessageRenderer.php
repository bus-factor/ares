<?php

declare(strict_types=1);

/**
 * ErrorMessageRenderer.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-25
 */

namespace Ares\Validation\Error;

use Ares\Validation\Context;

/**
 * Class ErrorMessageRenderer
 */
class ErrorMessageRenderer implements ErrorMessageRendererInterface
{
    /**
     * @param Context $context       Validation context.
     * @param string  $ruleId        Validation rule ID.
     * @param string  $message       Error message.
     * @param array   $substitutions Error message substitutions.
     * @return string
     */
    public function render(Context $context, string $ruleId, string $message, array $substitutions = []): string
    {
        return str_replace(
            array_map(function ($key) { return '{' . $key . '}'; }, array_keys($substitutions)),
            array_values($substitutions),
            $message
        );
    }
}
