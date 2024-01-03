<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Attributes\Tailor;

use ReflectionProperty;
use TodoMakeUsername\ObjectHelpers\Attributes\Shared\ObjectHelperAttributeInterface;

abstract class AbstractTailorAttribute implements ObjectHelperAttributeInterface
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
	 * Process the value and return any changes.
	 *
	 * @param mixed $value The value to process.
	 * @return mixed Returns the processed value.
	 */
	abstract public function process(mixed $value): mixed;
}