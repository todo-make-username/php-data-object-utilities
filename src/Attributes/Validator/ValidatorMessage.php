<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilities\Attributes\Validator;

use Attribute;
use TodoMakeUsername\DataObjectUtilities\Validator\ObjectValidatorException;

/**
 * This contains the validation message when a property fails validation with the specific validation attribute.
 *
 * Without this, a validation attribute would provide a very generic message about what went wrong.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ValidatorMessage
{
	/**
	 * The constructor
	 *
	 * @param string  $attribute_class The class this message attribute is for.
	 * @param string  $message         The message if the validation fails.
	 * @param boolean $throw_exception Determines if the validation should cleanly return or not.
	 */
	public function __construct(
		public string $attribute_class,
		public string $message,
		public bool $throw_exception=false
	)
	{
		if (!(is_subclass_of($attribute_class, AbstractValidatorAttribute::class)))
		{
			throw new ObjectValidatorException("'".$attribute_class."' must extend the AbstractValidatorAttribute class to be used with ValidatorMessage");
		}
	}
}