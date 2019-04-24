<?php

declare(strict_types=1);

/**
 * Schema.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace Ares\Schema;

use InvalidArgumentException;

/**
 * Class Schema
 */
class Schema
{
    /** @var array $rules */
    protected $rules = [];

    /**
     * @param string $ruleId Validation rule ID.
     * @return Rule
     * @throws InvalidArgumentException
     */
    public function getRule(string $ruleId): Rule
    {
        if (!$this->hasRule($ruleId)) {
            throw new InvalidArgumentException(sprintf('Rule not found in schema: %s', $ruleId));
        }

        return $this->rules[$ruleId];
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param string $ruleId Validation rule ID.
     * @return boolean
     */
    public function hasRule(string $ruleId): bool
    {
        return isset($this->rules[$ruleId]);
    }

    /**
     * @param Rule    $rule    Schema rule.
     * @param boolean $replace Indicate if the rule should be replace if already set.
     * @return self
     */
    public function setRule(Rule $rule, bool $replace = false): self
    {
        if ($replace || !isset($this->rules[$rule->getId()])) {
            $this->rules[$rule->getId()] = $rule;
        }

        return $this;
    }

    /**
     * @param array   $rules   Schema rules.
     * @param boolean $replace Indicate if the rule should be replace if already set.
     * @return self
     */
    public function setRules(array $rules, bool $replace = false): self
    {
        foreach ($rules as $rule) {
            $this->setRule($rule, $replace);
        }

        return $this;
    }
}

