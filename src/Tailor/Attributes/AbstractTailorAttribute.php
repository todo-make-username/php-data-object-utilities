<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Tailor\Attributes;

use ReflectionProperty;

abstract class AbstractTailorAttribute
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