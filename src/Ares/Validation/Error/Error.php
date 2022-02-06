<?php

declare(strict_types=1);

/**
 * Error.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-02-24
 */

namespace Ares\Validation\Error;

use JsonSerializable;

/**
 * Class Error
 */
class Error implements JsonSerializable
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $meta;

    /**
     * @var array
     */
    private $source;

    /**
     * Initializes the instance.
     *
     * @param array  $source  Error source (e.g. property path, param name).
     * @param string $code    Error code.
     * @param string $message Error message.
     * @param array  $meta    Error metadata.
     */
    public function __construct(
        array $source,
        string $code,
        string $message,
        array $meta = []
    ) {
        $this->code = $code;
        $this->message = $message;
        $this->meta = $meta;
        $this->source = $source;
    }

    /**
     * Returns the error code.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Returns the error message.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Returns the error metadata.
     *
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Returns the error source.
     *
     * @return array
     */
    public function getSource(): array
    {
        return $this->source;
    }

    /**
     * Returns the JSON serializable error representation.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'meta' => $this->meta,
            'source' => $this->source,
        ];
    }

    /**
     * Sets the error code.
     *
     * @param string $code Error code.
     * @return self
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Sets the error message.
     *
     * @param string $message Error message.
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Sets the error metadata.
     *
     * @param array $meta Error metadata.
     * @return self
     */
    public function setMeta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Sets the error source.
     *
     * @param array $source Error source.
     * @return self
     */
    public function setSource(array $source): self
    {
        $this->source = $source;

        return $this;
    }
}
