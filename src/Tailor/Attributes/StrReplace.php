<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Tailor\Attributes;

use Attribute;
use ReflectionProperty;
use TodoMakeUsername\ObjectHelpers\Tailor\Attributes\TailorAttributeInterface;

/**
 * Calls the str_replace() function on the value.
 *
 * The constructor args are the same ones used in str_replace. $subject and $count are omitted for obvious reasons.
 *
 * Can only be used on types that can be interpreted as a string.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class StrReplace implements TailorAttributeInterface
{
	public ReflectionProperty $Property;
	public bool $is_initialized = false;

	/**
	 * The constructor
	 *
	 * @param string|string[] $search  The value(s) to search for in the property's value.
	 * @param string|string[] $replace The replacement values(s) for ones that match $search.
	 */
	public function __construct(protected array|string $search, protected array|string $replace)
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

		$value = str_replace($this->search, $this->replace, $value);

		return $value;
	}
}