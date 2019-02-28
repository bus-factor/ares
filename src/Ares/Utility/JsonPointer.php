<?php

declare(strict_types=1);

/**
 * JsonPointer.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-02-28
 */

namespace Ares\Utility;

use Ares\Exception\StackEmptyException;

/**
 * Class JsonPointer
 */
class JsonPointer
{
    /** @var array $references */
    protected $references = [];

    /**
     * @return boolean
     */
    public function empty(): bool
    {
        return empty($this->references);
    }

    /**
     * @param string|null $jsonPointerString String-formatted JSON pointer.
     * @return self
     */
    public static function fromString(?string $jsonPointerString): self
    {
        $jsonPointer = new self();

        if ($jsonPointerString !== null) {
            $jsonPointer->references = array_map(
                function ($reference) {
                    return str_replace(['~1', '~0'], ['/', '~'], $reference);
                },
                explode('/', $jsonPointerString)
            );
        }

        return $jsonPointer;
    }

    /**
     * @return string
     * @throws \Ares\Exception\StackEmptyException
     */
    public function peek(): string
    {
        if (empty($this->references)) {
            throw new StackEmptyException();
        }

        return end($this->references);
    }

    /**
     * @return self
     */
    public function pop(): self
    {
        array_pop($this->references);

        return $this;
    }

    /**
     * @param string $reference Reference.
     * @return self
     */
    public function push(string $reference): self
    {
        array_push($this->references, $reference);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->references;
    }

    /**
     * @return string|null
     */
    public function toString(): ?string
    {
        if (empty($this->references)) {
            return null;
        }

        return implode(
            '/',
            array_map(
                function ($reference) {
                    return str_replace(['~', '/'], ['~0', '~1'], $reference);
                },
                $this->references
            )
        );
    }
}

