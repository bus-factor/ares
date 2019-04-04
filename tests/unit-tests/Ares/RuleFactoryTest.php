<?php

declare(strict_types=1);

/**
 * RuleFactoryTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-03
 */

namespace UnitTest\Ares;

use Ares\Context;
use Ares\Exception\UnknownValidationRuleIdException;
use Ares\RuleFactory;
use Ares\Rule\RuleInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class RuleFactoryTest
 *
 * @coversDefaultClass \Ares\RuleFactory
 */
class RuleFactoryTest extends TestCase
{
    /**
     * @covers ::get
     *
     * @testWith ["allowed",   "Ares\\Rule\\AllowedRule"]
     *           ["blankable", "Ares\\Rule\\BlankableRule"]
     *           ["datetime",  "Ares\\Rule\\DateTimeRule"]
     *           ["email",     "Ares\\Rule\\EmailRule"]
     *           ["forbidden", "Ares\\Rule\\ForbiddenRule"]
     *           ["maxlength", "Ares\\Rule\\MaxLengthRule"]
     *           ["max",       "Ares\\Rule\\MaxRule"]
     *           ["minlength", "Ares\\Rule\\MinLengthRule"]
     *           ["min",       "Ares\\Rule\\MinRule"]
     *           ["nullable",  "Ares\\Rule\\NullableRule"]
     *           ["regex",     "Ares\\Rule\\RegexRule"]
     *           ["required",  "Ares\\Rule\\RequiredRule"]
     *           ["type",      "Ares\\Rule\\TypeRule"]
     *           ["unknown",   "Ares\\Rule\\UnknownRule"]
     *           ["url",       "Ares\\Rule\\UrlRule"]
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
     * @covers ::isReserved
     *
     * @testWith ["allowed",   false]
     *           ["blankable", true]
     *           ["datetime",  false]
     *           ["email",     false]
     *           ["forbidden", false]
     *           ["maxlength", false]
     *           ["max",       false]
     *           ["minlength", false]
     *           ["min",       false]
     *           ["nullable",  true]
     *           ["regex",     false]
     *           ["required",  true]
     *           ["type",      true]
     *           ["unknown",   true]
     *           ["url",       false]
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

