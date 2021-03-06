<?php

declare(strict_types=1);

/**
 * Validator.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace Ares\Validation;

use Ares\Exception\InvalidOptionException;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Exception\UnknownValidationRuleIdException;
use Ares\Schema\Schema;
use Ares\Schema\SchemaList;
use Ares\Schema\SchemaMap;
use Ares\Schema\SchemaReference;
use Ares\Schema\SchemaTuple;
use Ares\Schema\Type;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Error\ErrorMessageRendererInterface;
use Ares\Validation\Rule\BlankableRule;
use Ares\Validation\Rule\NullableRule;
use Ares\Validation\Rule\RequiredRule;
use Ares\Validation\Rule\TypeRule;
use Ares\Validation\Rule\UnknownAllowedRule;
use BusFactor\Ddd\ValueObject\PhpType;

/**
 * Class Validator
 */
class Validator
{
    /**
     * @const array
     */
    private const OPTIONS_DEFAULTS = [
        Option::ALL_UNKNOWN_ALLOWED => false,
        Option::ALL_BLANKABLE       => false,
        Option::ALL_NULLABLE        => false,
        Option::ALL_REQUIRED        => true,
    ];

    /**
     * @var Context
     */
    private $context;

    /**
     * @var ErrorMessageRendererInterface
     */
    private $errorMessageRenderer;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @param Schema $schema Schema.
     */
    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return isset($this->context) ? $this->context->getErrors() : [];
    }

    /**
     * @return ErrorMessageRendererInterface
     */
    public function getErrorMessageRenderer(): ErrorMessageRendererInterface
    {
        if (!isset($this->errorMessageRenderer)) {
            $this->errorMessageRenderer = new ErrorMessageRenderer();
        }

        return $this->errorMessageRenderer;
    }

    /**
     * @param Schema $schema  Schema.
     * @param mixed  $data    Input data.
     * @param mixed  $field   Current field name or index.
     * @param array  $options Validation options.
     * @return void
     * @throws InvalidValidationRuleArgsException
     * @throws UnknownValidationRuleIdException
     */
    private function performValidation(
        Schema $schema,
        $data,
        $field,
        array $options
    ): void {
        if ($schema instanceof SchemaReference) {
            $schema = $schema->getSchema();
        }

        $this->context->enter($field, $schema);

        $skipRules = false;
        $builtInValidationsOk = $this->runBuiltinValidationRules(
            $schema,
            $data,
            $options,
            $skipRules
        );

        if ($builtInValidationsOk && !$skipRules) {
            $this->performFieldValidation($schema->getRules(), $data);

            switch ($schema->getRule(TypeRule::ID)->getArgs()) {
                case Type::LIST:
                    /** @var SchemaList $schema */
                    $this->performListValidation(
                        $schema->getSchema(),
                        $data,
                        $options
                    );

                    break;
                case Type::MAP:
                    /** @var SchemaMap $schema */
                    $this->performMapValidation(
                        $schema->getSchemas(),
                        $data,
                        $options
                    );

                    break;
                case Type::TUPLE:
                    /** @var SchemaTuple $schema */
                    $this->performMapValidation(
                        $schema->getSchemas(),
                        $data,
                        $options
                    );

                    break;
                default:
                    // no op
                    break;
            }
        }

        $this->context->leave();
    }

    /**
     * @param array $rules Schema rules.
     * @param mixed $data  Input data.
     * @return void
     */
    private function performFieldValidation(array $rules, $data): void
    {
        foreach ($rules as $ruleId => $rule) {
            if (RuleRegistry::isReserved($ruleId)) {
                continue;
            }

            $valid = RuleRegistry::get($ruleId)->validate(
                $rule->getArgs(),
                $data,
                $this->context
            );

            if (!$valid) {
                break;
            }
        }
    }

    /**
     * @param Schema $schema  Schema.
     * @param mixed  $data    Input data.
     * @param array  $options Validation options.
     * @return void
     */
    private function performListValidation(
        Schema $schema,
        $data,
        array $options
    ): void {
        foreach ($data as $key => $value) {
            $this->performValidation($schema, $value, $key, $options);
        }
    }

    /**
     * @param array $schemas Schemas.
     * @param mixed $data    Input data.
     * @param array $options Validation options.
     * @return void
     */
    private function performMapValidation(
        array $schemas,
        $data,
        array $options
    ): void {
        foreach ($schemas as $field => $schema) {
            $this->performValidation(
                $schema,
                $data[$field] ?? null,
                $field,
                $options
            );
        }
    }

    /**
     * @param array $options User provided options.
     * @return array
     * @throws InvalidOptionException
     */
    private function prepareOptions(array $options): array
    {
        foreach ($options as $key => $value) {
            if (!in_array($key, Option::getValidValues())) {
                $format = 'Unknown validation option key: \'%s\'';

                throw new InvalidOptionException(sprintf($format, $key));
            }

            $type = gettype($value);

            if ($type !== PhpType::BOOLEAN) {
                $format = 'Invalid validation option value: '
                    . '\'%s\' must be of type <boolean>, got <%s>';

                throw new InvalidOptionException(
                    sprintf($format, $key, $type)
                );
            }
        }

        return $options + self::OPTIONS_DEFAULTS;
    }

    /**
     * @param Schema $schema    Schema.
     * @param mixed  $data      Input data.
     * @param array  $options   Validation options.
     * @param bool   $skipRules Set if all following rules should be skipped.
     * @return bool
     */
    private function runBuiltinValidationRules(
        Schema $schema,
        $data,
        array $options,
        bool &$skipRules
    ): bool {
        $rules = [
            RequiredRule::ID       => $options[Option::ALL_REQUIRED],
            TypeRule::ID           => null,
            NullableRule::ID       => $options[Option::ALL_NULLABLE],
            UnknownAllowedRule::ID => $options[Option::ALL_UNKNOWN_ALLOWED],
            BlankableRule::ID      => $options[Option::ALL_BLANKABLE],
        ];

        $skipRules = false;

        foreach ($rules as $ruleId => $defaultRuleArgs) {
            $rule = RuleRegistry::get($ruleId);

            if ($rule->isApplicable($this->context)) {
                $ruleArgs = $schema->hasRule($ruleId)
                    ? $schema->getRule($ruleId)->getArgs()
                    : $defaultRuleArgs;

                if (!$rule->validate($ruleArgs, $data, $this->context)) {
                    return false;
                }

                if ($ruleId === NullableRule::ID && $data === null) {
                    $skipRules = true;
                }
            }
        }

        return true;
    }

    /**
     * @param ErrorMessageRendererInterface $errorMessageRenderer Error message renderer.
     * @return self
     */
    public function setErrorMessageRenderer(
        ErrorMessageRendererInterface $errorMessageRenderer
    ): self {
        $this->errorMessageRenderer = $errorMessageRenderer;

        return $this;
    }

    /**
     * @param mixed $data    Input data.
     * @param array $options Validation options.
     * @return boolean
     * @throws InvalidOptionException
     * @throws InvalidValidationRuleArgsException
     * @throws UnknownValidationRuleIdException
     */
    public function validate($data, array $options = []): bool
    {
        $options = $this->prepareOptions($options);
        $this->context = new Context($data, $this->getErrorMessageRenderer());

        $this->performValidation($this->schema, $data, '', $options);

        return !$this->context->hasErrors();
    }
}
