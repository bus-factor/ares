<?php

declare(strict_types=1);

/**
 * RuleFactoryTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-03
 */

namespace UnitTest\Ares\Validation;

use Ares\Exception\UnknownValidationRuleIdException;
use Ares\Validation\Context;
use Ares\Validation\RuleFactory;
use Ares\Validation\Rule\RuleInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class RuleFactoryTest
 *
 * @coversDefaultClass \Ares\Validation\RuleFactory
 */
class RuleFactoryTest extends TestCase
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
        $ruleFactory = new RuleFactory();

        $rule = $ruleFactory->get($ruleId);

        $this->assertInstanceOf($ruleFqcn, $rule);
        $this->assertSame($rule, $ruleFactory->get($ruleId));
    }

    /**
     * @covers ::get
     *
     * @return void
     */
    public function testGetRuleToHandleUnknownRuleIds(): void
    {
        $ruleId = 'bogus';
        $ruleFactory = new RuleFactory();

        $this->expectException(UnknownValidationRuleIdException::class);
        $this->expectExceptionMessage("Unknown validation rule ID: {$ruleId}");

        $ruleFactory->get($ruleId);
    }

    /**
     * @covers ::has
     *
     * @testWith ["required", true]
     *           ["bogus", false]
     *
     * @param string  $ruleId         Validation rule ID.
     * @param boolean $expectedRetVal Expected method return value.
     * @return void
     */
    public function testHas(string $ruleId, bool $expectedRetVal): void
    {
        $ruleFactory = new RuleFactory();

        $this->assertSame($expectedRetVal, $ruleFactory->has($ruleId));
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
        $ruleFactory = new RuleFactory();

        $this->assertSame($isReserved, $ruleFactory->isReserved($ruleId));
    }

    /**
     * @covers ::set
     *
     * @return void
     */
    public function testSet(): void
    {
        $ruleId = 'bogus';

        $rule = new class implements RuleInterface {
            public function validate($config, $data, Context $context): bool {
                return false;
            }
        };

        $ruleFactory = new RuleFactory();

        $this->assertSame($ruleFactory, $ruleFactory->set($ruleId, $rule));
        $this->assertSame($rule, $ruleFactory->get($ruleId));
    }
}

