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
use Ares\Rule\TypeRule;
use Ares\Utility\JsonPointer;

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
        ParserError::VALUE_TYPE_MISMATCH => 'Invalid validation schema value: %s must be of type <array>, got <%s>',
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
     * @param array $schema Validation schema.
     * @param array $source Current parsing position in the validation schema.
     * @return string
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function extractTypeOrFail(array $schema, array $source): string
    {
        $types = [];

        foreach ($schema as $key => $value) {
            if (is_string($key)) {
                if ($key == TypeRule::ID) {
                    $types[] = $value;
                }
            } elseif (is_int($key)) {
                if (is_array($value) && array_key_exists(TypeRule::ID, $value)) {
                    $types[] = $value[TypeRule::ID];
                }
            }
        }

        $n = count($types);

        if ($n < 1) {
            $this->fail(ParserError::TYPE_MISSING, $source);
        } elseif ($n > 1) {
            $this->fail(ParserError::TYPE_REPEATED, $source);
        }

        $type = reset($types);

        if (!in_array($type, Type::getValues(), true)) {
            $this->fail(ParserError::TYPE_UNKNOWN, $source, json_encode($type));
        }

        return $type;
    }

    /**
     * @param int    $parserError Parser error ID.
     * @param array  $source      Current parsing position in the validation schema.
     * @param array  $messageVars Variables to substiture in the message.
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function fail(int $parserError, array $source, ...$messageVars)
    {
        $sprintfArgs = array_merge(
            [self::ERROR_MESSAGES[$parserError]],
            [JsonPointer::encode($source)],
            $messageVars
        );

        throw new InvalidValidationSchemaException(call_user_func_array('sprintf', $sprintfArgs));
    }

    /**
     * @param mixed $schema Validation schema.
     * @param array $source Current parsing position in the validation schema.
     * @return array
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    public function parse($schema, array $source = ['']): array
    {
        $schemaType = gettype($schema);

        if ($schemaType !== 'array') {
            $this->fail(ParserError::VALUE_TYPE_MISMATCH, $source, $schemaType);
        }

        $type = $this->extractTypeOrFail($schema, $source);

        foreach ($schema as $key => $value) {
            if (is_string($key)) {
                $schema[$key] = $this->parseRule($type, array_merge($source, [$key]), $key, $value);
            } else {
                $schema[$key] = $this->parseRuleWithAdditions($type, array_merge($source, [$key]), $value);
            }
        }

        if (in_array($type, [Type::LIST, Type::MAP], true)) {
            if (!array_key_exists(Keyword::SCHEMA, $schema)) {
                $this->fail(ParserError::SCHEMA_MISSING, $source, $type);
            }

            if ($type === Type::LIST) {
                $schema[Keyword::SCHEMA] = $this->parse($schema[Keyword::SCHEMA], array_merge($source, [Keyword::SCHEMA]));
            } elseif ($type === Type::MAP) {
                $schemaType = gettype($schema[Keyword::SCHEMA]);

                if ($schemaType !== 'array') {
                    $this->fail(ParserError::VALUE_TYPE_MISMATCH, array_merge($source, [Keyword::SCHEMA]), $schemaType);
                }

                foreach ($schema[Keyword::SCHEMA] as $key => $value) {
                    $schema[Keyword::SCHEMA][$key] = $this->parse($value, array_merge($source, [Keyword::SCHEMA, $key]));
                }
            }
        }

        return $schema;
    }

    /**
     * @param string $type     Type according to validation schema.
     * @param array  $source   Current parsing position in the validation schema.
     * @param string $ruleId   Validation rule ID.
     * @param mixed  $ruleArgs Validation rule arguments.
     * @return mixed
     */
    protected function parseRule(string $type, array $source, string $ruleId, $ruleArgs)
    {
        if ($ruleId === Keyword::SCHEMA) {
            return $ruleArgs;
        }

        if (!$this->ruleFactory->has($ruleId)) {
            $this->fail(ParserError::RULE_ID_UNKNOWN, $source);
        }

        return $ruleArgs;
    }

    /**
     * @param string $type              Type according to validation schema.
     * @param array  $source            Current parsing position in the validation schema.
     * @param mixed  $additions Rule with additional information.
     * @return array
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function parseRuleWithAdditions(string $type, array $source, $additions): array
    {
        if (!is_array($additions)) {
            $this->fail(ParserError::VALUE_TYPE_MISMATCH, $source, gettype($additions));
        }

        $allowedAdditionalKeys = ['message'];
        $ruleIds = array_diff(array_keys($additions), $allowedAdditionalKeys);

        $n = count($ruleIds);

        if ($n < 1) {
            $this->fail(ParserError::RULE_MISSING, $source);
        } elseif ($n > 1) {
            $this->fail(ParserError::RULE_AMBIGUOUS, $source, json_encode($ruleIds));
        }

        $ruleId = reset($ruleIds);

        $additions[$ruleId] = $this->parseRule($type, array_merge($source, [$ruleId]), $ruleId, $additions[$ruleId]);

        return $additions;
    }
}

