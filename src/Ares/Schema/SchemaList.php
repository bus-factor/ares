<?php

declare(strict_types=1);

/**
 * SchemaList.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace Ares\Schema;

/**
 * Class SchemaList
 */
class SchemaList extends Schema
{
    /** @var Schema|null $schema */
    private $schema;

    /**
     * @return Schema|null
     */
    public function getSchema(): ?Schema
    {
        return $this->schema;
    }

    /**
     * @param Schema $schema Schema instance.
     * @return self
     */
    public function setSchema(Schema $schema): self
    {
        $this->schema = $schema;

        return $this;
    }
}

