<?php

declare(strict_types=1);

/**
 * RuleRegistryTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-28
 */

namespace UnitTest\Ares\Validation;

use Ares\Exception\UnknownValidationRuleIdException;
use Ares\Validation\Context;
use Ares\Validation\RuleRegistry;
use Ares\Validation\Rule\RuleInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class RuleRegistryTest
 *
 * @coversDefaultClass \Ares\Validation\RuleRegistry
 */
class RuleRegistryTest extends TestCase
{
    /**
     * @covers ::get
     *
     * @testWith ["allowed",        "Ares\\Validation\\Rule\\AllowedRule"]
     *           ["blankable",      "Ares\\Validation\\Rule\\BlankableRule"]
     *           ["datetime",       "Ares\\Validation\\Rule\\DateTimeRule"]
     *           ["directory",      "Ares\\Validation\\Rule\\DirectoryRule"]
     *           ["email",          "Ares\\Validation\\Rule\\EmailRule"]
     *           ["file",           "Ares\\Validation\\Rule\\FileRule"]
     *           ["forbidden",      "Ares\\Validation\\Rule\\ForbiddenRule"]
     *           ["length",         "Ares\\Validation\\Rule\\LengthRule"]
     *           ["maxlength",      "Ares\\Validation\\Rule\\MaxLengthRule"]
     *           ["max",            "Ares\\Validation\\Rule\\MaxRule"]
     *           ["minlength",      "Ares\\Validation\\Rule\\MinLengthRule"]
     *           ["min",            "Ares\\Validation\\Rule\\MinRule"]
     *           ["nullable",       "Ares\\Validation\\Rule\\NullableRule"]
     *           ["regex",          "Ares\\Validation\\Rule\\RegexRule"]
     *           ["required",       "Ares\\Validation\\Rule\\RequiredRule"]
     *           ["type",           "Ares\\Validation\\Rule\\TypeRule"]
     *           ["unknownAllowed", "Ares\\Validation\\Rule\\UnknownAllowedRule"]
     *           ["url",            "Ares\\Validation\\Rule\\UrlRule"]
     *
     * @param string $ruleId   Validation rule ID.
     * @param string $ruleFqcn Fully qualified class name of the validation rule.
     * @return void
     */
    public function testGetRule(string $ruleId, string $ruleFqcn): void
    {
        $rule = RuleRegistry::get($ruleId);

        $this->assertInstanceOf($ruleFqcn, $rule);
        $this->assertSame($rule, RuleRegistry::get($ruleId));
    }

    /**
     * @covers ::get
     * @covers ::unregisterAll
     *
     * @testWith ["allowed",        "Ares\\Validation\\Rule\\AllowedRule"]
     *           ["blankable",      "Ares\\Validation\\Rule\\BlankableRule"]
     *           ["datetime",       "Ares\\Validation\\Rule\\DateTimeRule"]
     *           ["directory",      "Ares\\Validation\\Rule\\DirectoryRule"]
     *           ["email",          "Ares\\Validation\\Rule\\EmailRule"]
     *           ["file",           "Ares\\Validation\\Rule\\FileRule"]
     *           ["forbidden",      "Ares\\Validation\\Rule\\ForbiddenRule"]
     *           ["length",         "Ares\\Validation\\Rule\\LengthRule"]
     *           ["maxlength",      "Ares\\Validation\\Rule\\MaxLengthRule"]
     *           ["max",            "Ares\\Validation\\Rule\\MaxRule"]
     *           ["minlength",      "Ares\\Validation\\Rule\\MinLengthRule"]
     *           ["min",            "Ares\\Validation\\Rule\\MinRule"]
     *           ["nullable",       "Ares\\Validation\\Rule\\NullableRule"]
     *           ["regex",          "Ares\\Validation\\Rule\\RegexRule"]
     *           ["required",       "Ares\\Validation\\Rule\\RequiredRule"]
     *           ["type",           "Ares\\Validation\\Rule\\TypeRule"]
     *           ["unknownAllowed", "Ares\\Validation\\Rule\\UnknownAllowedRule"]
     *           ["url",            "Ares\\Validation\\Rule\\UrlRule"]
     *
     * @param string $ruleId   Validation rule ID.
     * @param string $ruleFqcn Fully qualified class name of the validation rule.
     * @return void
     */
    public function testGetRuleRecoversBuiltInRulesAfterUnregistration(string $ruleId, string $ruleFqcn): void
    {
        $rule = RuleRegistry::get($ruleId);

        $this->assertInstanceOf($ruleFqcn, $rule);
        $this->assertSame($rule, RuleRegistry::get($ruleId));

        RuleRegistry::unregisterAll();

        $this->assertInstanceOf($ruleFqcn, RuleRegistry::get($ruleId));
        $this->assertNotSame($rule, RuleRegistry::get($ruleId));
    }

    /**
     * @covers ::get
     *
     * @return void
     */
    public function testGetRuleToHandleUnknownRuleIds(): void
    {
        $ruleId = 'bogus';

        $this->expectException(UnknownValidationRuleIdException::class);
        $this->expectExceptionMessage("Unknown validation rule ID: {$ruleId}");

        RuleRegistry::get($ruleId);
    }

    /**
     * @covers ::isRegistered
     *
     * @testWith ["required", true]
     *           ["bogus", false]
     *
     * @param string  $ruleId         Validation rule ID.
     * @param boolean $expectedRetVal Expected method return value.
     * @return void
     */
    public function testIsRegistered(string $ruleId, bool $expectedRetVal): void
    {
        $this->assertSame($expectedRetVal, RuleRegistry::isRegistered($ruleId));
    }

    /**
     * @covers ::isReserved
     *
     * @testWith ["allowed",        false]
     *           ["blankable",      true]
     *           ["datetime",       false]
     *           ["directory",      false]
     *           ["email",          false]
     *           ["file",           false]
     *           ["forbidden",      false]
     *           ["maxlength",      false]
     *           ["max",            false]
     *           ["minlength",      false]
     *           ["min",            false]
     *           ["nullable",       true]
     *           ["regex",          false]
     *           ["required",       true]
     *           ["type",           true]
     *           ["unknownAllowed", true]
     *           ["url",            false]
     *
     * @param string  $ruleId     Validation rule ID.
     * @param boolean $isReserved Indicator if rule is reserved.
     * @return void
     */
    public function testIsReserved(string $ruleId, bool $isReserved): void
    {
        $this->assertSame($isReserved, RuleRegistry::isReserved($ruleId));
    }

    /**
     * @covers ::get
     * @covers ::register
     *
     * @return void
     */
    public function testRegister(): void
    {
        $ruleId = 'bogus';

        $rule = new class implements RuleInterface {
            public function isApplicable(Context $context): bool {
                return true;
            }

            public function validate($config, $data, Context $context): bool {
                return false;
            }
        };

        RuleRegistry::register($ruleId, $rule);

        $this->assertSame($rule, RuleRegistry::get($ruleId));
    }

    /**
     * @covers ::get
     * @covers ::isRegistered
     * @covers ::register
     * @covers ::unregister
     *
     * @return void
     */
    public function testRegisterAndUnregister(): void
    {
        $ruleId1 = 'foo';

        $rule1 = new class implements RuleInterface {
            public function isApplicable(Context $context): bool {
                return true;
            }

            public function validate($config, $data, Context $context): bool {
                return false;
            }
        };

        RuleRegistry::register($ruleId1, $rule1);

        $ruleId2 = 'bar';

        $rule2 = new class implements RuleInterface {
            public function isApplicable(Context $context): bool {
                return true;
            }

            public function validate($config, $data, Context $context): bool {
                return false;
            }
        };

        RuleRegistry::register($ruleId2, $rule2);

        $this->assertSame($rule1, RuleRegistry::get($ruleId1));
        $this->assertSame($rule2, RuleRegistry::get($ruleId2));

        $this->assertTrue(RuleRegistry::isRegistered($ruleId1));
        $this->assertTrue(RuleRegistry::isRegistered($ruleId2));

        RuleRegistry::unregister($ruleId1);

        $this->assertFalse(RuleRegistry::isRegistered($ruleId1));
        $this->assertTrue(RuleRegistry::isRegistered($ruleId2));

        RuleRegistry::unregister($ruleId2);

        $this->assertFalse(RuleRegistry::isRegistered($ruleId1));
        $this->assertFalse(RuleRegistry::isRegistered($ruleId2));
    }

    /**
     * @covers ::unregister
     *
     * @return void
     */
    public function testUnregisterHandlesUnknownRuleIds(): void
    {
        $this->expectException(UnknownValidationRuleIdException::class);
        $this->expectExceptionMessage('Unknown validation rule: cannot unregister <foobar>');

        RuleRegistry::unregister('foobar');
    }
}

