<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Hydrator\Attributes;

use Attribute;
use TodoMakeUsername\ObjectHelpers\Hydrator\ObjectHydrationException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Required implements HydratorAttributeInterface
{
	/**
	 * The Constructor
	 *
	 * @param boolean $not_empty The value must also not be empty.
	 */
	public function __construct(protected bool $not_empty=false)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value, array $meta_data): mixed
	{
		$Property = $meta_data['Property'];
		$property_name = $Property->name;

		if (!($meta_data['is_set'] ?? true)) {
			throw new ObjectHydrationException("a value is required for '{$property_name}'.");
		}

		if ($this->not_empty && empty($value)) {
			throw new ObjectHydrationException("a value is required and must not be empty for '{$property_name}'.");
		}

		return $value;
	}
}