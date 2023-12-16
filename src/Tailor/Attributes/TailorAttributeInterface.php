<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Tailor\Attributes;

interface TailorAttributeInterface
{
	/**
	 * Process the value and return any changes.
	 *
	 * @param mixed $value The value to process.
	 * @return mixed Returns the processed value.
	 */
	public function process(mixed $value): mixed;
}