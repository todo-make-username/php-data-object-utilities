<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Hydrator\Attributes;

use Attribute;
use ReflectionProperty;
use TodoMakeUsername\ObjectHelpers\Hydrator\ObjectHydrationException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Required implements HydratorAttributeInterface
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
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		$Property      = $this->Property;
		$property_name = $Property->name;

		if (!$this->is_set)
		{
			throw new ObjectHydrationException("A value is required for '{$property_name}'.");
		}

		return $value;
	}
}