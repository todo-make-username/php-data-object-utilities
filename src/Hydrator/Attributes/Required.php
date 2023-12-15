<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Hydrator\Attributes;

use Attribute;
use TodoMakeUsername\ObjectHelpers\Hydrator\ObjectHydrationException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Required implements HydratorAttributeInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value, array $meta_data=[]): mixed
	{
		$Property      = $meta_data['Property'];
		$property_name = $Property->name;

		if (!($meta_data['is_set'] ?? true))
		{
			throw new ObjectHydrationException("A value is required for '{$property_name}'.");
		}

		return $value;
	}
}