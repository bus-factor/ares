<?php

declare(strict_types=1);

/**
 * Parser.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-07
 */

namespace Ares\Schema;

use Ares\Exception\InvalidValidationSchemaException;
use Ares\RuleFactory;
use Ares\Rule\RequiredRule;
use Ares\Rule\TypeRule;
use Ares\Rule\UnknownRule;
use Ares\Utility\JsonPointer;
use Ares\Utility\PhpType;

/**
 * Class Parser
 */
class Parser
{
    /** @const array ERROR_MESSAGES */
    private const ERROR_MESSAGES = [
        ParserError::RULE_AMBIGUOUS      => 'Invalid validation schema: %s contains multiple rules (%s)',
        ParserError::RULE_ID_UNKNOWN     => 'Unknown validation rule ID: %s specifies an unknown validation rule ID',
        ParserError::RULE_MISSING        => 'Invalid validation schema: %s contains no rule',
        ParserError::SCHEMA_MISSING      => 'Missing validation schema key: %s uses type "%s" but contains no "schema" key',
        ParserError::TYPE_MISSING        => 'Insufficient validation schema: %s contains no `type` validation rule',
        ParserError::TYPE_REPEATED       => 'Ambiguous validation schema: %s contains multiple `type` validation rules',
        ParserError::TYPE_UNKNOWN        => 'Invalid validation schema: %s uses unknown type: %s',
        ParserError::VALUE_TYPE_MISMATCH => 'Invalid validation schema value: %s must be of type <%s>, got <%s>',
        ParserError::RULE_INAPPLICABLE   => 'Invalid validation schema: %s validation rule is not applicable to type <%s>',
    ];

    /** @const array VALID_RULE_ADDITION_KEYS */
    private const VALID_RULE_ADDITION_KEYS = [
        Keyword::MESSAGE,
        Keyword::META,
    ];

    /** @const array SCHEMA_FQCNS_BY_TYPE */
    private const SCHEMA_FQCNS_BY_TYPE = [
        Type::BOOLEAN => Schema::class,
        Type::FLOAT   => Schema::class,
        Type::INTEGER => Schema::class,
        Type::LIST    => SchemaList::class,
        Type::MAP     => SchemaMap::class,
        Type::NUMERIC => Schema::class,
        Type::STRING  => Schema::class,
        Type::TUPLE   => SchemaTuple::class,
    ];

    /** @param \Ares\RuleFactory $ruleFactory */
    protected $ruleFactory;

    /**
     * @param \Ares\RuleFactory $ruleFactory
     */
    public function __construct(RuleFactory $ruleFactory)
    {
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * @param \Ares\Schema\ParserContext $context Parser context.
     * @return void
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function ascertainInputHoldsArrayOrFail(ParserContext $context): void
    {
        $type = gettype($context->getInput());

        if ($type !== PhpType::ARRAY) {
            $this->fail(ParserError::VALUE_TYPE_MISMATCH, $context, PhpType::ARRAY, $type);
        }
    }

    /**
     * @param \Ares\Schema\ParserContext $context Parser context.
     * @return string
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function extractTypeOrFail(ParserContext $context): string
    {
        $types = [];

        foreach ($context->getInput() as $key => $value) {
            if (is_string($key)) {
                if ($key == TypeRule::ID) {
                    $types[] = $value;
                }
            } elseif (isset($value[TypeRule::ID])) {
                $types[] = $value[TypeRule::ID];
            }
        }

        $typesCount = count($types);

        if ($typesCount < 1) {
            $this->fail(ParserError::TYPE_MISSING, $context);
        } elseif ($typesCount > 1) {
            $this->fail(ParserError::TYPE_REPEATED, $context);
        }

        $type = $types[0];

        if (!in_array($type, Type::getValues(), true)) {
            $this->fail(ParserError::TYPE_UNKNOWN, $context, json_encode($type));
        }

        return $type;
    }

    /**
     * @param int                        $parserError Parser error ID.
     * @param \Ares\Schema\ParserContext $context     Parser context.
     * @param array                      $messageVars Variables to substiture in the message.
     * @throws \Ares\Exception\InvalidValidationSchemaException
     * @see \Ares\Schema\ParserError
     */
    protected function fail(int $parserError, ParserContext $context, ...$messageVars)
    {
        $message = self::ERROR_MESSAGES[$parserError];
        $source = JsonPointer::encode($context->getInputPosition());
        $sprintfArgs = array_merge([$message, $source], $messageVars);

        throw new InvalidValidationSchemaException(call_user_func_array('sprintf', $sprintfArgs));
    }

    /**
     * @param mixed $schema Validation schema.
     * @return \Ares\Schema\Schema
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    public function parse($schema): Schema
    {
        $context = new ParserContext($schema, '');

        return $this->parseSchema($context);
    }

    /**
     * @param string                     $type    Value type.
     * @param \Ares\Schema\ParserContext $context Parser context.
     * @param string                     $ruleId  Validation rule ID.
     * @return \Ares\Schema\Rule|null
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function parseRule(string $type, ParserContext $context, string $ruleId): ?Rule
    {
        $rule = null;

        $context->enter($ruleId);

        if ($ruleId !== Keyword::SCHEMA) {
            if (!$this->ruleFactory->has($ruleId)) {
                $this->fail(ParserError::RULE_ID_UNKNOWN, $context);
            }

            $validationRule = $this->ruleFactory->get($ruleId);

            if (method_exists($validationRule, 'getSupportedTypes')) {
                $supportedTypes = $validationRule->getSupportedTypes();

                if (!in_array($type, $supportedTypes, true)) {
                    $this->fail(ParserError::RULE_INAPPLICABLE, $context, $type);
                }
            }

            $rule = new Rule($ruleId, $context->getInput());
        }

        $context->leave();

        return $rule;
    }

    /**
     * @param string                     $type    Value type.
     * @param \Ares\Schema\ParserContext $context Parser context.
     * @param mixed                      $index   Parser context related index.
     * @return \Ares\Schema\Rule|null
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function parseRuleWithAdditions(string $type, ParserContext $context, $index): ?Rule
    {
        $context->enter($index);

        $this->ascertainInputHoldsArrayOrFail($context);

        $input = $context->getInput();
        $ruleIds = array_diff(array_keys($input), self::VALID_RULE_ADDITION_KEYS);
        $ruleIdsCount = count($ruleIds);

        if ($ruleIdsCount < 1) {
            $this->fail(ParserError::RULE_MISSING, $context);
        } elseif ($ruleIdsCount > 1) {
            $this->fail(ParserError::RULE_AMBIGUOUS, $context, json_encode($ruleIds));
        }

        $ruleId = reset($ruleIds);
        $rule = $this->parseRule($type, $context, $ruleId);

        if ($rule !== null) {
            if (isset($input[Keyword::MESSAGE])) {
                $type = gettype($input[Keyword::MESSAGE]);

                if ($type !== PhpType::STRING) {
                    $context->enter(Keyword::MESSAGE);

                    $this->fail(ParserError::VALUE_TYPE_MISMATCH, $context, PhpType::STRING, $type);
                }

                $rule->setMessage($input[Keyword::MESSAGE]);
            }

            if (isset($input[Keyword::META])) {
                $type = gettype($input[Keyword::META]);

                if ($type !== PhpType::ARRAY) {
                    $context->enter(Keyword::META);

                    $this->fail(ParserError::VALUE_TYPE_MISMATCH, $context, PhpType::ARRAY, $type);
                }

                $rule->setMeta($input[Keyword::META]);
            }
        }

        $context->leave();

        return $rule;
    }

    /**
     * @param \Ares\Schema\ParserContext $context Parser context.
     * @return \Ares\Schema\Schema
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function parseSchema(ParserContext $context): Schema
    {
        $this->ascertainInputHoldsArrayOrFail($context);

        $type = $this->extractTypeOrFail($context);
        $schemaFqcn = self::SCHEMA_FQCNS_BY_TYPE[$type];
        $schema = new $schemaFqcn();
        $keys = array_keys($context->getInput());

        foreach ($keys as $key) {
            $rule = is_string($key)
                ? $this->parseRule($type, $context, $key)
                : $this->parseRuleWithAdditions($type, $context, $key);

            if ($rule !== null) {
                $schema->setRule($rule);
            }
        }

        if (in_array($type, [Type::LIST, Type::MAP, Type::TUPLE], true)) {
            if (!array_key_exists(Keyword::SCHEMA, $context->getInput())) {
                $this->fail(ParserError::SCHEMA_MISSING, $context, $type);
            }

            $context->enter(Keyword::SCHEMA);

            switch ($type) {
                case Type::LIST:
                    $schema->setSchema($this->parseSchema($context));

                    break;
                case Type::MAP:
                    $schema->setSchemas($this->parseSchemas($context));

                    break;
                case Type::TUPLE:
                    // no break
                default:
                    $schema->setSchemas($this->parseTupleSchemas($context));

                    if (!$schema->hasRule(UnknownRule::ID)) {
                        $schema->setRule(new Rule(UnknownRule::ID, false));
                    }

                    $schema->getRule(UnknownRule::ID)->setArgs(false);

                    break;
            }

            $context->leave();
        }

        return $schema;
    }

    /**
     * @param \Ares\Schema\ParserContext $context Parser context.
     * @return array
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function parseSchemas(ParserContext $context): array
    {
        $schemas = [];

        $this->ascertainInputHoldsArrayOrFail($context);

        $keys = array_keys($context->getInput());

        foreach ($keys as $key) {
            $context->enter($key);

            $schemas[$key] = $this->parseSchema($context);

            $context->leave();
        }

        return $schemas;
    }

    /**
     * @param \Ares\Schema\ParserContext $context Parser context.
     * @return array
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function parseTupleSchemas(ParserContext $context): array
    {
        $schemas = $this->parseSchemas($context);

        foreach ($schemas as $schema) {
            if (!$schema->hasRule(RequiredRule::ID)) {
                $schema->setRule(new Rule(RequiredRule::ID, true));
            }

            $schema->getRule(RequiredRule::ID)->setArgs(true);
        }

        return $schemas;
    }
}

