<?php

declare(strict_types=1);

/**
 * ErrorMessageRendererInterface.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-26
 */

namespace Ares\Validation\Error;

use Ares\Validation\Context;

/**
 * Interface ErrorMessageRendererInterface
 */
interface ErrorMessageRendererInterface
{
    /**
     * @param Context $context       Validation context.
     * @param string  $ruleId        Validation rule ID.
     * @param string  $message       Error message.
     * @param array   $substitutions Error message substitutions.
     * @return string
     */
    public function render(
        Context $context,
        string $ruleId,
        string $message,
        array $substitutions = []
    ): string;
}
