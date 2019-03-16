<?php

declare(strict_types=1);

/**
 * Context.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace Ares\Validation;

/**
 * Class Context
 */
class Context
{
    /** @var array $errors */
    protected $errors = [];
    /** @var array $source */
    protected $source = [];

    /**
     * @param string $code    Error code.
     * @param string $message Error message.
     * @param array  $meta    Error metadata.
     * @return self
     */
    public function addError(string $code, string $message, array $meta = []): self
    {
        $this->errors[] = new Error($this->source, $code, $message, $meta);

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getSource(): array
    {
        return $this->source;
    }

    /**
     * @return boolean
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @return self
     */
    public function popSourceReference(): self
    {
        array_pop($this->source);

        return $this;
    }

    /**
     * @param mixed $reference Source reference.
     * @return self
     */
    public function pushSourceReference($reference): self
    {
        $this->source[] = $reference;

        return $this;
    }
}

