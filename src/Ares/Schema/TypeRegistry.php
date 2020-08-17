<?php

declare(strict_types=1);

/**
 * TypeRegistry.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-27
 */

namespace Ares\Schema;

use Ares\Exception\InvalidSchemaException;
use InvalidArgumentException;

/**
 * Class TypeRegistry
 */
class TypeRegistry
{
    /** @var array|callable[] $schemas */
    private static $schemas = [];

    /**
     * @param string $type Type name.
     * @return Schema
     * @throws InvalidArgumentException
     * @throws InvalidSchemaException
     */
    public static function get(string $type): Schema
    {
        if (!isset(self::$schemas[$type])) {
            throw new InvalidArgumentException(sprintf('Unknown type: type <%s> is not registered', $type));
        }

        $callable = self::$schemas[$type];

        return $callable();
    }

    /**
     * @param string $type Type name.
     * @return bool
     */
    public static function isRegistered(string $type): bool
    {
        return isset(self::$schemas[$type]);
    }

    /**
     * @param string $type   Type name.
     * @param array  $schema Schema.
     * @return void
     * @throws InvalidArgumentException
     */
    public static function register(string $type, array $schema): void
    {
        if (in_array($type, Type::getValues(), true)) {
            throw new InvalidArgumentException(sprintf('Reserved type: <%s> is a built-in type and must not be overwritten', $type));
        }

        self::$schemas[$type] = function () use ($schema): Schema {
            return (new Parser())->parse($schema);
        };
    }

    /**
     * @param string $type Type name.
     * @return void
     */
    public static function unregister(string $type): void
    {
        if (!isset(self::$schemas[$type])) {
            throw new InvalidArgumentException(sprintf('Unknown type: cannot unregister <%s>', $type));
        }

        unset(self::$schemas[$type]);
    }

    /**
     * @return void
     */
    public static function unregisterAll(): void
    {
        self::$schemas = [];
    }
}

