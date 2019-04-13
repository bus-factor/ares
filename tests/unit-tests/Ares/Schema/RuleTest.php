<?php

declare(strict_types=1);

/**
 * RuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace UnitTest\Ares\Schema;

use Ares\Schema\Rule;
use PHPUnit\Framework\TestCase;

/**
 * Class RuleTest
 *
 * @coversDefaultClass \Ares\Schema\Rule
 */
class RuleTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getArgs
     * @covers ::setArgs
     *
     * @return void
     */
    public function testArgsAccessors(): void
    {
        $id = 'foo';
        $args = 42;
        $argsNew = 1337;

        $rule = new Rule($id, $args);

        $this->assertSame($args, $rule->getArgs());
        $this->assertSame($rule, $rule->setArgs($argsNew));
        $this->assertSame($argsNew, $rule->getArgs());
    }

    /**
     * @covers ::__construct
     * @covers ::getId
     * @covers ::setId
     *
     * @return void
     */
    public function testIdAccessors(): void
    {
        $id = 'foo';
        $idNew = 'bar';
        $args = ['asdf' => '234'];

        $rule = new Rule($id, $args);

        $this->assertSame($id, $rule->getId());
        $this->assertSame($rule, $rule->setId($idNew));
        $this->assertSame($idNew, $rule->getId());
    }

    /**
     * @covers ::__construct
     * @covers ::getMessage
     * @covers ::setMessage
     *
     * @return void
     */
    public function testMessageAccessors(): void
    {
        $id = 'foo';
        $args = ['asdf' => '234'];
        $message = 'This value is not valid';
        $messageNew = 'This value is invalid';

        $rule = new Rule($id, $args);

        $this->assertNull($rule->getMessage());

        $rule = new Rule($id, $args, $message);

        $this->assertSame($message, $rule->getMessage());
        $this->assertSame($rule, $rule->setMessage($messageNew));
        $this->assertSame($messageNew, $rule->getMessage());
    }

    /**
     * @covers ::__construct
     * @covers ::getMeta
     * @covers ::setMeta
     *
     * @return void
     */
    public function testMetaAccessors(): void
    {
        $id = 'foo';
        $args = ['asdf' => '234'];
        $message = null;
        $meta = ['a' => 'b'];
        $metaNew = ['c' => 'd'];

        $rule = new Rule($id, $args);

        $this->assertNull($rule->getMeta());

        $rule = new Rule($id, $args, $message, $meta);

        $this->assertSame($meta, $rule->getMeta());
        $this->assertSame($rule, $rule->setMeta($metaNew));
        $this->assertSame($metaNew, $rule->getMeta());
    }
}

