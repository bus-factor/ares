<?php

declare(strict_types=1);

/**
 * JsonPointer.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-02-28
 */

namespace Ares\Utility;

use JsonSerializable;

/**
 * Class JsonPointer
 */
class JsonPointer extends Stack implements JsonSerializable
{
    /**
     * Decodes an encoded reference within a JSON pointer string.
     *
     * @param string $encodedReference Encoded reference.
     * @return string
     */
    public static function decodeReference(string $encodedReference): string
    {
        return str_replace(['~1', '~0'], ['/', '~'], $encodedReference);
    }

    /**
     * Encodes a reference to use it safely within a JSON pointer string.
     *
     * @param string $reference Plain reference.
     * @return string
     */
    public static function encodeReference(string $reference): string
    {
        return str_replace(['~', '/'], ['~0', '~1'], $reference);
    }

    /**
     * Creates a JSON pointer object from its string representation.
     *
     * @param string|null $jsonPointer String-formatted JSON pointer.
     * @return self
     */
    public static function fromString(?string $jsonPointer): self
    {
        return ($jsonPointer === null)
            ? new JsonPointer()
            : new JsonPointer(array_map([self::class, 'decodeReference'], explode('/', $jsonPointer)));
    }

    /**
     * Returns a JSON serializable representation of the JSON pointer.
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->toString();
    }

    /**
     * Returns the JSON pointer string representation of the instance.
     *
     * @return string|null
     */
    public function toString(): ?string
    {
        return $this->empty()
            ? null
            : implode('/', array_map([self::class, 'encodeReference'], $this->getElements()));
    }
}

