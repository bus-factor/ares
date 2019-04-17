<?php

declare(strict_types=1);

/**
 * RuleFactory.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-03
 */

namespace Ares;

use Ares\Exception\UnknownValidationRuleIdException;
use Ares\Rule\AllowedRule;
use Ares\Rule\BlankableRule;
use Ares\Rule\DateTimeRule;
use Ares\Rule\DirectoryRule;
use Ares\Rule\EmailRule;
use Ares\Rule\FileRule;
use Ares\Rule\ForbiddenRule;
use Ares\Rule\MaxLengthRule;
use Ares\Rule\MaxRule;
use Ares\Rule\MinLengthRule;
use Ares\Rule\MinRule;
use Ares\Rule\NullableRule;
use Ares\Rule\RegexRule;
use Ares\Rule\RequiredRule;
use Ares\Rule\RuleInterface;
use Ares\Rule\TypeRule;
use Ares\Rule\UnknownRule;
use Ares\Rule\UrlRule;

/**
 * Class RuleFactory
 */
class RuleFactory
{
    /** @var array $ruleFqcns */
    protected $ruleFqcns = [
        AllowedRule::ID   => ['className' => AllowedRule::class,   'reserved' => false],
        BlankableRule::ID => ['className' => BlankableRule::class, 'reserved' => true],
        DateTimeRule::ID  => ['className' => DateTimeRule::class,  'reserved' => false],
        DirectoryRule::ID => ['className' => DirectoryRule::class, 'reserved' => false],
        EmailRule::ID     => ['className' => EmailRule::class,     'reserved' => false],
        FileRule::ID      => ['className' => FileRule::class,      'reserved' => false],
        ForbiddenRule::ID => ['className' => ForbiddenRule::class, 'reserved' => false],
        MaxLengthRule::ID => ['className' => MaxLengthRule::class, 'reserved' => false],
        MaxRule::ID       => ['className' => MaxRule::class,       'reserved' => false],
        MinLengthRule::ID => ['className' => MinLengthRule::class, 'reserved' => false],
        MinRule::ID       => ['className' => MinRule::class,       'reserved' => false],
        NullableRule::ID  => ['className' => NullableRule::class,  'reserved' => true],
        RegexRule::ID     => ['className' => RegexRule::class,     'reserved' => false],
        RequiredRule::ID  => ['className' => RequiredRule::class,  'reserved' => true],
        TypeRule::ID      => ['className' => TypeRule::class,      'reserved' => true],
        UnknownRule::ID   => ['className' => UnknownRule::class,   'reserved' => true],
        UrlRule::ID       => ['className' => UrlRule::class,       'reserved' => false],
    ];

    /** @var \Ares\Rule\RuleInterface[] $rules */
    protected $rules = [];

    /**
     * @param string $ruleId Validation rule ID.
     * @return \Ares\Rule\RuleInterface
     */
    public function get(string $ruleId): RuleInterface
    {
        if (!isset($this->rules[$ruleId])) {
            if (!isset($this->ruleFqcns[$ruleId])) {
                throw new UnknownValidationRuleIdException("Unknown validation rule ID: {$ruleId}");
            }

            $ruleFqcn = $this->ruleFqcns[$ruleId]['className'];
            $this->rules[$ruleId] = new $ruleFqcn();
        }

        return $this->rules[$ruleId];
    }

    /**
     * @param string $ruleId Rule ID.
     * @return bool
     */
    public function has(string $ruleId): bool
    {
        return isset($this->rules[$ruleId]) || isset($this->ruleFqcns[$ruleId]);
    }

    /**
     * @param string $ruleId Validation rule ID.
     * @return bool
     */
    public function isReserved(string $ruleId): bool
    {
        return !empty($this->ruleFqcns[$ruleId]['reserved']);
    }

    /**
     * @param string $ruleId           Validation rule ID.
     * @param \Ares\Rule\RuleInterface Validation rule instance.
     * @return self
     */
    public function set(string $ruleId, RuleInterface $rule): self
    {
        $this->rules[$ruleId] = $rule;

        return $this;
    }
}

