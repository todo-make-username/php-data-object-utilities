<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Validator\Attributes;

interface ValidatorAttributeInterface
{
	/**
	 * Validate the value.
	 *
	 * @param mixed $value The value to validate.
	 * @return boolean Returns if the validation passed.
	 */
	public function process(mixed $value): bool;
}