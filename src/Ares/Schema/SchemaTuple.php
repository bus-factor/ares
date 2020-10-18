<?php

declare(strict_types=1);

/**
 * SchemaTuple.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-18
 */

namespace Ares\Schema;

/**
 * Class SchemaTuple
 */
class SchemaTuple extends Schema
{
    /** @var array $schemas */
    private $schemas = [];

    /**
     * @return array
     */
    public function getSchemas(): array
    {
        return $this->schemas;
    }

    /**
     * @param Schema $schema Schema instance.
     * @return self
     */
    public function appendSchema(Schema $schema): self
    {
        $this->schemas[] = $schema;

        return $this;
    }

    /**
     * @param array $schemas Schema instances.
     * @return self
     */
    public function setSchemas(array $schemas): self
    {
        foreach ($schemas as $schema) {
            $this->appendSchema($schema);
        }

        return $this;
    }
}

