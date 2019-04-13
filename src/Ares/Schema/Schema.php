<?php

declare(strict_types=1);

/**
 * Schema.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace Ares\Schema;

/**
 * Class Schema
 */
class Schema
{
    /** @var array $rules */
    protected $rules = [];

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param \Ares\Schema\Rule $rule    Schema rule.
     * @param boolean           $replace Indicate if the rule should be replace if already set.
     * @return self
     */
    public function setRule(Rule $rule, bool $replace = false): self
    {
        if ($replace || !isset($this->rules[$rule->getId()])) {
            $this->rules[$rule->getId()] = $rule;
        }

        return $this;
    }
}

