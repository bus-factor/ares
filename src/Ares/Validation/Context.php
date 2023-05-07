<?php

declare(strict_types=1);

/**
 * Context.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace Ares\Validation;

use Ares\Validation\Error\Error;
use Ares\Validation\Error\ErrorMessageRendererInterface;
use Ares\Schema\Schema;

/**
 * Class Context
 */
class Context
{
    private mixed $data;

    /**
     * @var array<Error>
     */
    private array $errors = [];

    private ErrorMessageRendererInterface $errorMessageRenderer;

    private array $schemas = [];

    private array $source = [];

    public function __construct(
        mixed &$data,
        ErrorMessageRendererInterface $errorMessageRenderer
    ) {
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

    public function &getData(): mixed
    {
        return $this->data;
    }

    /**
     * @return array<Error>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorMessageRenderer(): ErrorMessageRendererInterface
    {
        return $this->errorMessageRenderer;
    }

    public function getSchema(): Schema
    {
        return end($this->schemas);
    }

    public function getSource(): array
    {
        return $this->source;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function enter(string|int $reference, Schema $schema): self
    {
        $this->source[] = $reference;
        $this->schemas[] = $schema;

        return $this;
    }

    public function leave(): self
    {
        array_pop($this->source);
        array_pop($this->schemas);

        return $this;
    }
}
