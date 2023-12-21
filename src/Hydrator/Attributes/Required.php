<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Hydrator\Attributes;

use Attribute;
use TodoMakeUsername\ObjectHelpers\Hydrator\ObjectHydrationException;

/**
 * The hydration data array must have this property name set as a key.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Required extends AbstractHydratorAttribute
{
	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		$property_name = $this->Property->name;

		if (!$this->is_set)
		{
			throw new ObjectHydrationException("A value is required for '{$property_name}'.");
		}

		return $value;
	}
}