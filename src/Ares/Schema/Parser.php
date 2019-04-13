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
     * @param \Ares\Schema\ParserContext $context Parser context.
     * @return void
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function ascertainInputHoldsArrayOrFail(ParserContext $context): void
    {
        $type = gettype($context->getInput());

        if ($type !== 'array') {
            $this->fail(ParserError::VALUE_TYPE_MISMATCH, $context, $type);
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

        $n = count($types);

        if ($n < 1) {
            $this->fail(ParserError::TYPE_MISSING, $context);
        } elseif ($n > 1) {
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
        $sprintfArgs = array_merge(
            [self::ERROR_MESSAGES[$parserError]],
            [JsonPointer::encode($context->getInputPosition())],
            $messageVars
        );

        throw new InvalidValidationSchemaException(call_user_func_array('sprintf', $sprintfArgs));
    }

    /**
     * @param mixed $schema Validation schema.
     * @return array
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    public function parse($schema): array
    {
        $context = new ParserContext($schema, '');

        return $this->parseSchema($context);
    }

    /**
     * @param \Ares\Schema\ParserContext $context Parser context.
     * @param string                     $type    Type according to validation schema.
     * @param string                     $ruleId  Validation rule ID.
     * @return void
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function parseRule(ParserContext $context, string $type, string $ruleId): void
    {
        $context->enter($ruleId);

        if ($ruleId !== Keyword::SCHEMA) {
            if (!$this->ruleFactory->has($ruleId)) {
                $this->fail(ParserError::RULE_ID_UNKNOWN, $context);
            }
        }

        $context->leave();
    }

    /**
     * @param \Ares\Schema\ParserContext $context Parser context.
     * @param string                     $type    Type according to validation schema.
     * @param mixed                      $index   Parser context related index.
     * @return array
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function parseRuleWithAdditions(ParserContext $context, string $type, $index): void
    {
        $context->enter($index);

        $this->ascertainInputHoldsArrayOrFail($context);

        $allowedAdditionalKeys = ['message'];
        $ruleIds = array_diff(array_keys($context->getInput()), $allowedAdditionalKeys);

        $n = count($ruleIds);

        if ($n < 1) {
            $this->fail(ParserError::RULE_MISSING, $context);
        } elseif ($n > 1) {
            $this->fail(ParserError::RULE_AMBIGUOUS, $context, json_encode($ruleIds));
        }

        $ruleId = reset($ruleIds);

        $this->parseRule($context, $type, $ruleId);

        $context->leave();
    }

    /**
     * @param \Ares\Schema\ParserContext $context Parser context.
     * @return array
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function parseSchema(ParserContext $context): array
    {
        $this->ascertainInputHoldsArrayOrFail($context);

        $type = $this->extractTypeOrFail($context);

        foreach ($context->getInput() as $key => $value) {
            if (is_string($key)) {
                $this->parseRule($context, $type, $key);
            } else {
                $this->parseRuleWithAdditions($context, $type, $key);
            }
        }

        if (in_array($type, [Type::LIST, Type::MAP], true)) {
            if (!array_key_exists(Keyword::SCHEMA, $context->getInput())) {
                $this->fail(ParserError::SCHEMA_MISSING, $context, $type);
            }

            $context->enter(Keyword::SCHEMA);

            if ($type === Type::LIST) {
                $this->parseSchema($context);
            } else { // $type === Type::MAP
                $this->ascertainInputHoldsArrayOrFail($context);

                foreach ($context->getInput() as $key => $value) {
                    $this->parseSchemaMap($context, $key);
                }
            }

            $context->leave();
        }

        return $context->getInput();
    }

    /**
     * @param \Ares\Schema\ParserContext $context               Parser context.
     * @param mixed                      $relativeInputPosition Relative parser context input position.
     * @return void
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    public function parseSchemaMap(ParserContext $context, $relativeInputPosition): void
    {
        $context->enter($relativeInputPosition);
        $this->parseSchema($context);
        $context->leave();
    }
}

