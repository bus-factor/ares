<?php

declare(strict_types=1);

/**
 * Context.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace Ares;

/**
 * Class Context
 */
class Context
{
    /** @var mixed $data */
    protected $data;
    /** @var array $errors */
    protected $errors = [];
    /** @var array $schemas */
    protected $schemas = [];
    /** @var array $source */
    protected $source = [];

    /**
     * @param mixed $data Input data.
     */
    public function __construct(&$data = null)
    {
        $this->data = &$data;
    }

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
     * @return mixed
     */
    public function &getData()
    {
        return $this->data;
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
    public function getSchema(): array
    {
        return end($this->schemas);
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
     * @param mixed $reference Source reference.
     * @param array $schema    Source specific validation schema.
     * @return self
     */
    public function enter($reference, array $schema): self
    {
        $this->source[] = $reference;
        $this->schemas[] = $schema;

        return $this;
    }

    /**
     * @return self
     */
    public function leave(): self
    {
        array_pop($this->source);
        array_pop($this->schemas);

        return $this;
    }
}

