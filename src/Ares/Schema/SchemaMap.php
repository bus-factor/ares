<?php

declare(strict_types=1);

/**
 * SchemaMap.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace Ares\Schema;

/**
 * Class SchemaMap
 */
class SchemaMap extends Schema
{
    /** @var array $schemas */
    protected $schemas = [];

    /**
     * @return array
     */
    public function getSchemas(): array
    {
        return $this->schemas;
    }

    /**
     * @param string              $field  Field name.
     * @param \Ares\Schema\Schema $schema Schema instance.
     * @return self
     */
    public function setSchema(string $field, Schema $schema): self
    {
        $this->schemas[$field] = $schema;

        return $this;
    }
}

