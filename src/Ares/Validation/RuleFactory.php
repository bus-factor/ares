<?php

declare(strict_types=1);

/**
 * RuleFactory.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-03
 */

namespace Ares\Validation;

use Ares\Exception\UnknownValidationRuleIdException;
use Ares\Validation\Rule\AllowedRule;
use Ares\Validation\Rule\BlankableRule;
use Ares\Validation\Rule\DateTimeRule;
use Ares\Validation\Rule\DirectoryRule;
use Ares\Validation\Rule\EmailRule;
use Ares\Validation\Rule\FileRule;
use Ares\Validation\Rule\ForbiddenRule;
use Ares\Validation\Rule\LengthRule;
use Ares\Validation\Rule\MaxLengthRule;
use Ares\Validation\Rule\MaxRule;
use Ares\Validation\Rule\MinLengthRule;
use Ares\Validation\Rule\MinRule;
use Ares\Validation\Rule\NullableRule;
use Ares\Validation\Rule\RegexRule;
use Ares\Validation\Rule\RequiredRule;
use Ares\Validation\Rule\RuleInterface;
use Ares\Validation\Rule\TypeRule;
use Ares\Validation\Rule\UnknownAllowedRule;
use Ares\Validation\Rule\UrlRule;

/**
 * Class RuleFactory
 */
class RuleFactory
{
    /** @var array $ruleFqcns */
    protected $ruleFqcns = [
        AllowedRule::ID        => ['className' => AllowedRule::class,        'reserved' => false],
        BlankableRule::ID      => ['className' => BlankableRule::class,      'reserved' => true],
        DateTimeRule::ID       => ['className' => DateTimeRule::class,       'reserved' => false],
        DirectoryRule::ID      => ['className' => DirectoryRule::class,      'reserved' => false],
        EmailRule::ID          => ['className' => EmailRule::class,          'reserved' => false],
        FileRule::ID           => ['className' => FileRule::class,           'reserved' => false],
        ForbiddenRule::ID      => ['className' => ForbiddenRule::class,      'reserved' => false],
        LengthRule::ID         => ['className' => LengthRule::class,         'reserved' => false],
        MaxLengthRule::ID      => ['className' => MaxLengthRule::class,      'reserved' => false],
        MaxRule::ID            => ['className' => MaxRule::class,            'reserved' => false],
        MinLengthRule::ID      => ['className' => MinLengthRule::class,      'reserved' => false],
        MinRule::ID            => ['className' => MinRule::class,            'reserved' => false],
        NullableRule::ID       => ['className' => NullableRule::class,       'reserved' => true],
        RegexRule::ID          => ['className' => RegexRule::class,          'reserved' => false],
        RequiredRule::ID       => ['className' => RequiredRule::class,       'reserved' => true],
        TypeRule::ID           => ['className' => TypeRule::class,           'reserved' => true],
        UnknownAllowedRule::ID => ['className' => UnknownAllowedRule::class, 'reserved' => true],
        UrlRule::ID            => ['className' => UrlRule::class,            'reserved' => false],
    ];

    /** @var RuleInterface[] $rules */
    protected $rules = [];

    /**
     * @param string $ruleId Validation rule ID.
     * @return RuleInterface
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
     * @param string        $ruleId Validation rule ID.
     * @param RuleInterface $rule   Validation rule instance.
     * @return self
     */
    public function set(string $ruleId, RuleInterface $rule): self
    {
        $this->rules[$ruleId] = $rule;

        return $this;
    }
}

