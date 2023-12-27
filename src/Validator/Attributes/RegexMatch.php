<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Validator\Attributes;

use Attribute;
use TodoMakeUsername\ObjectHelpers\Util\StringHelper;
use TodoMakeUsername\ObjectHelpers\Validator\ObjectValidatorException;

/**
 * The value in the attribute must not be empty.
 *
 * This ignores uninitialized properties.
 *
 * Can only be used on types that can be interpreted as a string. Others are ignored.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class RegexMatch extends AbstractValidatorAttribute
{

	/**
	 * The constructor
	 *
	 * @param string $pattern The regex pattern to validate this property.
	 */
	public function __construct(protected string $pattern='')
	{}

	/**
	 * {@inheritDoc}
	 */
	public function getFailMessage(): string
	{
		return 'The "'.$this->Property->name.'" field must match the following pattern: '.$this->pattern;
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate(mixed $value): bool
	{
		if (!$this->is_initialized)
		{
			return true;
		}

		if (!StringHelper::isStringCompatible($value))
		{
			return true;
		}

		$value = strval($value);

		$match_result = preg_match($this->pattern, $value);

		if ($match_result === null) {
			throw new ObjectValidatorException("Invalid pattern used to validate field. '".$this->pattern."'");
		}

		return ($match_result === 1);
	}
}