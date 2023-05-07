<?php

declare(strict_types=1);

/**
 * JsonPointer.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-02-28
 */

namespace Ares\Utility;

use InvalidArgumentException;

/**
 * Class JsonPointer
 */
class JsonPointer
{
    /**
     * @param string $jsonPointer JSON pointer.
     * @return array
     */
    public static function decode(string $jsonPointer): array
    {
        $references = [];
        $encodedReferences = explode('/', $jsonPointer);

        foreach ($encodedReferences as $encodedReference) {
            $references[] = str_replace(['~1', '~0'], ['/', '~'], (string) $encodedReference);
        }

        return $references;
    }

    /**
     * @param array $references References.
     * @return string
     * @throw InvalidArgumentException
     */
    public static function encode(array $references): string
    {
        if (empty($references)) {
            throw new InvalidArgumentException('Cannot encode JSON pointer without references');
        }

        $encodedReferences = [];

        foreach ($references as $reference) {
            $encodedReferences[] = str_replace(['~', '/'], ['~0', '~1'], (string) $reference);
        }

        return implode('/', $encodedReferences);
    }
}
