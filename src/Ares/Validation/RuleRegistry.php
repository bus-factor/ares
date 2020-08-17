<?php

declare(strict_types=1);

/**
 * RuleRegistry.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-28
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
use Ares\Validation\Rule\UuidRule;

/**
 * Class RuleRegistry
 */
class RuleRegistry
{
    /**
     * @const array
     */
    private const BUILT_IN_RULE_FQCNS = [
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
        UuidRule::ID           => ['className' => UuidRule::class,           'reserved' => false],
    ];

    /**
     * @var array|RuleInterface[]
     */
    private static $rules = [];

    /**
     * @param string $ruleId Validation rule ID.
     * @return RuleInterface
     * @throws UnknownValidationRuleIdException
     */
    public static function get(string $ruleId): RuleInterface
    {
        if (!isset(self::$rules[$ruleId])) {
            if (!isset(self::BUILT_IN_RULE_FQCNS[$ruleId])) {
                throw new UnknownValidationRuleIdException(
                    'Unknown validation rule ID: ' . $ruleId
                );
            }

            $ruleFqcn = self::BUILT_IN_RULE_FQCNS[$ruleId]['className'];
            self::$rules[$ruleId] = new $ruleFqcn();
        }

        return self::$rules[$ruleId];
    }

    /**
     * @param string $ruleId Validation rule ID.
     * @return bool
     */
    public static function isRegistered(string $ruleId): bool
    {
        return isset(self::$rules[$ruleId])
            || isset(self::BUILT_IN_RULE_FQCNS[$ruleId]);
    }

    /**
     * @param string $ruleId Validation rule ID.
     * @return bool
     */
    public static function isReserved(string $ruleId): bool
    {
        return !empty(self::BUILT_IN_RULE_FQCNS[$ruleId]['reserved']);
    }

    /**
     * @param string        $ruleId Validation rule ID.
     * @param RuleInterface $rule   Validation rule instance.
     * @return void
     */
    public static function register(string $ruleId, RuleInterface $rule): void
    {
        self::$rules[$ruleId] = $rule;
    }

    /**
     * @param string $ruleId Validation rule ID.
     * @return void
     */
    public static function unregister(string $ruleId): void
    {
        if (!isset(self::$rules[$ruleId])) {
            throw new UnknownValidationRuleIdException(
                'Unknown validation rule ID: ' . $ruleId
            );
        }

        unset(self::$rules[$ruleId]);
    }

    /**
     * @return void
     */
    public static function unregisterAll(): void
    {
        self::$rules = [];
    }
}
