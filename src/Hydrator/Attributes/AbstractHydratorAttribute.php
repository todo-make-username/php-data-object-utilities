<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Hydrator\Attributes;

use ReflectionProperty;
use TodoMakeUsername\ObjectHelpers\Shared\Attributes\ObjectHelperAttributeInterface;

/**
 * The interface for all hydration attributes.
 *
 * Hydration attributes are hydrated themselves with certain pieces of metadata:
 *
 * ReflectionProperty:$Property, bool:$is_set
 */
abstract class AbstractHydratorAttribute implements ObjectHelperAttributeInterface
{
	/**
	 * The reflection object of the property this attribute is on.
	 *
	 * @var ReflectionProperty
	 */
	public ReflectionProperty $Property;

	/**
	 * If the data was passed in with the hydration data or not.
	 *
	 * @var boolean
	 */
	public bool $is_set = false;


	/**
	 * Process the value before hydration.
	 *
	 * @param mixed $value The value to process.
	 * @return mixed Returns the processed value.
	 */
	abstract public function process(mixed $value): mixed;
}