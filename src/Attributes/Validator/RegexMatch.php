<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilities\Attributes\Validator;

use Attribute;
use TodoMakeUsername\DataObjectUtilities\Util\StringHelper;
use TodoMakeUsername\DataObjectUtilities\Validator\ObjectValidatorException;

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
		if (!StringHelper::isStringCompatible($value))
		{
			return false;
		}

		// The @ suppresses the warning when the pattern is invalid.
		// This is intentional as this checks the validity of the pattern before using it.
		if (@preg_match($this->pattern, '') === false)
		{
			throw new ObjectValidatorException("Invalid pattern used to validate '".$this->Property->name."': '".$this->pattern."'");
		}

		$value        = strval($value);
		$match_result = preg_match($this->pattern, $value);

		return ($match_result === 1);
	}
}