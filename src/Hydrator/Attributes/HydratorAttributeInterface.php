<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Hydrator\Attributes;

use TodoMakeUsername\ObjectHelpers\Shared\Attributes\ObjectHelperAttributeInterface;

/**
 * The interface for all hydration attributes.
 *
 * Hydration attributes are hydrated themselves with certain pieces of metadata:
 *
 * ReflectionProperty:$Property, bool:$is_set
 */
interface HydratorAttributeInterface extends ObjectHelperAttributeInterface
{
	/**
	 * Process the value before hydration.
	 *
	 * @param mixed $value The value to process.
	 * @return mixed Returns the processed value.
	 */
	public function process(mixed $value): mixed;
}