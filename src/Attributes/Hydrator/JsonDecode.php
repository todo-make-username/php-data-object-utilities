<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilities\Attributes\Hydrator;

use Attribute;
use TodoMakeUsername\DataObjectUtilities\Hydrator\ObjectHydrationException;
use TodoMakeUsername\DataObjectUtilities\Util\StringHelper;

/**
 * When a valid json string is passed in, turn it into an array.
 *
 * Can only be used on properties that can take an array.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class JsonDecode extends AbstractHydratorAttribute
{

	/**
	 * Decode a json string.
	 *
	 * All arguments match what PHP's json_decode takes.
	 *
	 * @param boolean|null $associative If this value should be parsed as an associative array.
	 * @param integer      $depth       Specified recursion depth.
	 * @param integer      $flags       Bit mask of JSON decode options.
	 */
	public function __construct(protected readonly ?bool $associative=null, protected readonly int $depth=512, protected readonly int $flags=0)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		// Don't do anything if already an array or the value is empty.
		if (is_array($value) || empty($value))
		{
			return $value;
		}

		$property_name = $this->Property->name;

		if (!StringHelper::isStringCompatible($value))
		{
			throw new ObjectHydrationException("'{$property_name}' requires an array or string compatible value.");
		}

		$str_value = strval($value);

		// TODO: 8.3 json_validate
		$json_value = json_decode($str_value, $this->associative, $this->depth, $this->flags);
		if (json_last_error() === JSON_ERROR_NONE && !empty($json_value))
		{
			$value = $json_value;
		}

		if (!is_array($value))
		{
			throw new ObjectHydrationException("Failed to hydrate '{$property_name}', a valid JSON string is required.");
		}

		return $value;
	}
}