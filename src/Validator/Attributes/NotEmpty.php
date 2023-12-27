<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Validator\Attributes;

use Attribute;

/**
 * The value in the attribute must not be empty.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class NotEmpty extends AbstractValidatorAttribute
{
	/**
	 * {@inheritDoc}
	 */
	public function getFailMessage(): string
	{
		return 'The "'.$this->Property->name.'" field must contain a value.';
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate(mixed $value): bool
	{
		return (!empty($value));
	}
}