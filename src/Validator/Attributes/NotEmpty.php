<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Validator\Attributes;

use Attribute;
use ReflectionProperty;

/**
 * The value in the attribute must not be empty.
 *
 * This ignores uninitialized properties.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class NotEmpty extends AbstractValidatorAttribute
{
	/**
	 * The constructor
	 *
	 * @param string $fail_message The message used when this property fails validation on this attribute.
	 */
	public function __construct(public string $fail_message="")
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): bool
	{
		return (!empty($value));
	}
}