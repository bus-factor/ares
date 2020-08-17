<?php

declare(strict_types=1);

/**
 * SchemaReference.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-28
 */

namespace Ares\Schema;

/**
 * Class SchemaReference
 */
class SchemaReference extends Schema
{
    /**
     * @param Schema
     */
    private $schema;

    /**
     * @return Schema
     */
    public function &getSchema(): Schema
    {
        return $this->schema;
    }

    /**
     * @param Schema $schema Schema reference.
     * @return self
     */
    public function setSchema(Schema &$schema): self
    {
        $this->schema = &$schema;

        return $this;
    }
}
