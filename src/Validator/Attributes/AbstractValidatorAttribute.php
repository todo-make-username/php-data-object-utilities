<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Validator\Attributes;

use ReflectionProperty;
use TodoMakeUsername\ObjectHelpers\Shared\Attributes\ObjectHelperAttributeInterface;

abstract class AbstractValidatorAttribute implements ObjectHelperAttributeInterface
{
	/**
	 * The reflection object of the property this attribute is on.
	 *
	 * @var ReflectionProperty
	 */
	public ReflectionProperty $Property;

	/**
	 * Determines if the object property was initialized or not.
	 *
	 * This will ALWAYS be true for non-typed properties. Blame ReflectionProperty not me.
	 *
	 * @var boolean
	 */
	public bool $is_initialized = false;

	/**
	 * Validate the value.
	 *
	 * @param mixed $value The value to validate.
	 * @return boolean Returns if the validation passed.
	 */
	abstract public function validate(mixed $value): bool;

	/**
	 * Get the generic message to use if this validation does not pass.
	 *
	 * @return string
	 */
	abstract public function getFailMessage(): string;
}