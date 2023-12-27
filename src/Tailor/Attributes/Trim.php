<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Tailor\Attributes;

use Attribute;
use TodoMakeUsername\ObjectHelpers\Util\StringHelper;

/**
 * Calls the trim() function on the value.
 *
 * Can only be used on types that can be interpreted as a string. Others are ignored.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Trim extends AbstractTailorAttribute
{
	/**
	 * The constructor
	 *
	 * @param string|null $characters Any Optional characters to trim.
	 */
	public function __construct(protected ?string $characters=null)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		if (!is_string($value))
		{
			return $value;
		}

		if (is_null($this->characters))
		{
			$value = trim($value);
		}
		else
		{
			$value = trim($value, $this->characters);
		}

		return $value;
	}
}