<?php

declare(strict_types=1);

/**
 * Sanitizer.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-10
 */

namespace Ares\Schema;

use Ares\Exception\InvalidValidationSchemaException;

/**
 * Class Sanitizer
 */
class Sanitizer
{
    /**
     * @param array $schema         Validation schema.
     * @param array $schemaDefaults Validation schema default values.
     * @return array
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    public static function sanitize(array $schema, array $schemaDefaults): array
    {
        return self::performSanitization([], $schema, $schemaDefaults);
    }

    /**
     * @param array $source         Current position in schema.
     * @param array $schema         Validation schema.
     * @param array $schemaDefaults Validation schema default values.
     * @return array
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected static function performSanitization(array $source, array $schema, array $schemaDefaults): array
    {
        $schema += $schemaDefaults;

        if (!isset($schema['type'])) {
            $sourceFormatted = implode('\'][\'', array_merge($source, ['type']));
            $message = sprintf('Missing schema option: $schema[\'%s\']', $sourceFormatted);

            throw new InvalidValidationSchemaException($message);
        }

        if (!in_array($schema['type'], Type::getValues(), true)) {
            $sourceFormatted = implode('\'][\'', array_merge($source, ['type']));
            $message = sprintf('Invalid schema option value: $schema[\'%s\'] = \'%s\'', $sourceFormatted, $schema['type']);

            throw new InvalidValidationSchemaException($message);
        }

        if ($schema['type'] === Type::MAP) {
            if (isset($schema['schema'])) {
                if (!is_array($schema['schema'])) {
                    $type = gettype($schema['schema']);
                    $sourceFormatted = implode('\'][\'', array_merge($source, ['schema']));
                    $message = sprintf('Expected <array>, got <%s>: $schema[\'%s\']', $type, $sourceFormatted);

                    throw new InvalidValidationSchemaException($message);
                }

                foreach ($schema['schema'] as $field => $fieldSchema) {
                    $schema['schema'][$field] = self::performSanitization(
                        array_merge($source, ['schema', $field]),
                        $fieldSchema,
                        $schemaDefaults
                    );
                }
            } else {
                $schema['schema'] = [];
            }
        } elseif ($schema['type'] === Type::LIST) {
            if (!isset($schema['schema'])) {
                $sourceFormatted = implode('\'][\'', array_merge($source, ['schema']));
                $message = sprintf('Missing schema option: $schema[\'%s\']', $sourceFormatted);

                throw new InvalidValidationSchemaException($message);
            }

            $schema['schema'] = self::performSanitization(
                array_merge($source, ['schema']),
                $schema['schema'],
                $schemaDefaults
            );
        }

        return $schema;
    }
}

