<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Validator\Attributes;

use Attribute;
use ReflectionProperty;
use TodoMakeUsername\ObjectHelpers\Validator\ObjectValidationFailureException;

/**
 * The value in the attribute must not be empty.
 *
 * This ignores uninitialized properties.
 *
 * Can only be used on types that can be interpreted as a string. Others are ignored.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Pattern extends AbstractValidatorAttribute
{

	/**
	 * The constructor
	 *
	 * @param string $fail_message The message used when this property fails validation on this attribute.
	 * @param string $pattern      The regex pattern to validate this property.
	 */
	public function __construct(public string $fail_message="", protected string $pattern='')
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): bool
	{
		if (!$this->is_initialized)
		{
			return true;
		}

		if (!is_string($value))
		{
			return true;
		}

		$match_result = preg_match($this->pattern, $value);

		if ($match_result === null) {
			throw new ObjectValidationFailureException("Invalid pattern used to validate field. '".$this->pattern."'");
		}

		return ($match_result === 1);
	}
}