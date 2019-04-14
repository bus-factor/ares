<?php

declare(strict_types=1);

/**
 * Context.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace Ares;

use Ares\Error\Error;
use Ares\Error\ErrorMessageRendererInterface;
use Ares\Schema\Schema;

/**
 * Class Context
 */
class Context
{
    /** @var mixed $data */
    protected $data;
    /** @var array $errors */
    protected $errors = [];
    /** @var \Ares\Error\ErrorMessageRendererInterface $errorMessageRenderer */
    protected $errorMessageRenderer;
    /** @var array $schemas */
    protected $schemas = [];
    /** @var array $source */
    protected $source = [];

    /**
     * @param mixed                                     $data                 Input data.
     * @param \Ares\Error\ErrorMessageRendererInterface $errorMessageRenderer Error message renderer.
     */
    public function __construct(&$data, ErrorMessageRendererInterface $errorMessageRenderer)
    {
        $this->data = &$data;
        $this->errorMessageRenderer = $errorMessageRenderer;
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
     * @return \Ares\Error\ErrorMessageRendererInterface
     */
    public function getErrorMessageRenderer(): ErrorMessageRendererInterface
    {
        return $this->errorMessageRenderer;
    }

    /**
     * @return \Ares\Schema\Schema
     */
    public function getSchema(): Schema
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
     * @param mixed               $reference Source reference.
     * @param \Ares\Schema\Schema $schema    Source specific validation schema.
     * @return self
     */
    public function enter($reference, Schema $schema): self
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

