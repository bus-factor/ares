<?php

declare(strict_types=1);

/**
 * Rule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace Ares\Schema;

/**
 * Class Rule
 */
class Rule
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var mixed
     */
    private $args;

    /**
     * @var string|null
     */
    private $message;

    /**
     * @var array
     */
    private $meta;

    /**
     * @param string      $id      Validation rule ID.
     * @param mixed       $args    Validation rule arguments.
     * @param string|null $message Custom validation error message.
     * @param array|null  $meta    Validation error meta data.
     */
    public function __construct(
        string $id,
        $args,
        ?string $message = null,
        ?array $meta = null
    ) {
        $this->id = $id;
        $this->args = $args;
        $this->message = $message;
        $this->meta = $meta ?? [];
    }

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @param mixed $args Validation rule arguments.
     * @return self
     */
    public function setArgs($args): self
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @param string $id Validation rule ID.
     * @return self
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string|null $message Custom validation error message.
     * @return self
     */
    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param array $meta Validation error meta data.
     * @return self
     */
    public function setMeta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }
}
