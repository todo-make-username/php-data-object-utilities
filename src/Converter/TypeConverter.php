<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilities\Converter;

use TodoMakeUsername\DataObjectUtilities\Util\StringHelper;

class TypeConverter
{
	public static array $type_method_map = [
		'string'  => 'convertToString',
		'bool'    => 'convertToBool',
		'boolean' => 'convertToBool',
		'int'     => 'convertToInt',
		'integer' => 'convertToInt',
		'float'   => 'convertToFloat',
		'double'  => 'convertToFloat',
		'array'   => 'convertToEmptyArray', // Only empty value conversion. Everything else needs to use Hydration Attributes like the provided JsonDecode.
		'null'    => 'convertToMixed',
		'mixed'   => 'convertToMixed',
		// No Object Conversion
	];

	/**
	 * Convert value to a specific type.
	 *
	 * Conversion to these types only: string, boolean, integer, float/double, mixed/no strict type
	 *
	 * Objects can be converted if __toString() is implemented. Or at least we can try.
	 *
	 * @param mixed  $value    The value to convert.
	 * @param string $type     The type to convert to.
	 * @param array  $metadata Any optional data that might be needed.
	 * @return mixed
	 */
	public static function convertTo(mixed $value, string $type, array $metadata=[]): mixed
	{
		$method_name = self::$type_method_map[$type] ?? null;

		// Conversion is not supported, simply return the value.
		if (is_null($method_name))
		{
			return $value;
		}

		// The object can be converted to string via __toString(), so lets do that.
		if (gettype($value) === 'object' && StringHelper::isStringCompatible($value))
		{
			$value = strval($value);
		}

		return TypeConverter::$method_name($value, $metadata);
	}

	/**
	 * Convert value to a mixed value lol
	 *
	 * @param mixed $value    The value to convert.
	 * @param array $metadata Any optional data that might be needed.
	 * @return string
	 */
	public static function convertToMixed(mixed $value, array $metadata=[]): mixed
	{
		// Omg, most complex method ever
		($metadata); // For removing the unused warning.
		return $value;
	}

	/**
	 * Convert value to a string
	 *
	 * @param mixed $value    The value to convert.
	 * @param array $metadata Any optional data that might be needed.
	 * @return string
	 */
	public static function convertToString(mixed $value, array $metadata=[]): string
	{
		($metadata); // For removing the unused warning.
		$new_value = '';

		try
		{
			$new_value = strval($value);
		}
		catch (\Throwable $th)
		{
			throw new ConversionException('Failed to convert '.gettype($value).' to string.');
		}

		$value = $new_value;
		return $value;
	}

	/**
	 * Convert value to an integer
	 *
	 * @param mixed $value    The value to convert.
	 * @param array $metadata Any optional data that might be needed.
	 * @return integer
	 */
	public static function convertToInt(mixed $value, array $metadata=[]): int
	{
		// filter_var INT Note: Characters after numbers will work, but not the other way around:
		//     aa123 will fail, 123aa will convert to 123.
		$new_value = filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
		$strict    = $metadata['strict'] ?? true;

		if (!$strict && is_null($new_value))
		{
			$new_value = intval($value);
		}

		if (is_null($new_value))
		{
			throw new ConversionException('Failed to convert '.gettype($value).' to int');
		}

		$value = $new_value;
		return $value;
	}

	/**
	 * Convert value to a float
	 *
	 * @param mixed $value    The value to convert.
	 * @param array $metadata Any optional data that might be needed.
	 * @return float
	 */
	public static function convertToFloat(mixed $value, array $metadata=[]): float
	{
		$new_value = filter_var($value, FILTER_VALIDATE_FLOAT, (FILTER_FLAG_ALLOW_THOUSAND | FILTER_NULL_ON_FAILURE));
		$strict    = $metadata['strict'] ?? true;

		if (!$strict && is_null($new_value))
		{
			$new_value = floatval($value);
		}

		if (is_null($new_value))
		{
			throw new ConversionException('Failed to convert '.gettype($value).' to float');
		}

		$value = $new_value;
		return $value;
	}

	/**
	 * Convert value to a boolean
	 *
	 * @param mixed $value    The value to convert.
	 * @param array $metadata Any optional data that might be needed.
	 * @return boolean
	 */
	public static function convertToBool(mixed $value, array $metadata=[]): bool
	{
		// This also checks for on/off, yes/no, "true"/"false", 1/0
		$new_value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		$strict    = $metadata['strict'] ?? true;

		if (!$strict && is_null($new_value))
		{
			$new_value = boolval($value);
		}

		if (is_null($new_value))
		{
			throw new ConversionException('Failed to convert '.gettype($value).' to bool');
		}

		$value = $new_value;
		return $value;
	}

	/**
	 * Convert an empty value to an empty array.
	 *
	 * Empty values only. Everything else will fail.
	 *
	 * @param mixed $value    The value to convert.
	 * @param array $metadata Any optional data that might be needed.
	 * @return array
	 */
	public static function convertToEmptyArray(mixed $value, array $metadata=[]): array
	{
		($metadata);
		$new_value = $value ?: [];

		if (!is_array($new_value))
		{
			throw new ConversionException('Failed to convert '.gettype($value).' to array.');
		}

		$value = $new_value;
		return $value;
	}
}