<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Hydrator\Attributes;

interface HydratorAttributeInterface
{
	/**
	 * Process the value before hydration.
	 *
	 * @param mixed $value     The value to process.
	 * @param array $meta_data Any metadata that might be needed.
	 * @return mixed Returns the processed value.
	 */
	public function process(mixed $value, array $meta_data): mixed;
}