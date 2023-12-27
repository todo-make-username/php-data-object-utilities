<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Converter;

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
		'array'   => 'convertToArray',
		'null'    => 'convertToMixed',
		'mixed'   => 'convertToMixed',
		// No Object Conversion
	];

	/**
	 * Convert value to a specific type.
	 *
	 * If the desired type is not a basic PHP data type, or is an object, no conversion happens.
	 *
	 * @param mixed  $value    The value to convert.
	 * @param string $type     The type to convert to.
	 * @param array  $metadata Any optional data that might be needed.
	 * @return mixed
	 */
	public static function convertTo(mixed $value, string $type, array $metadata=[]): mixed
	{
		$method_name = self::$type_method_map[$type] ?? null;

		if (is_null($method_name))
		{
			return $value;
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
			throw new ConversionException('Failed to convert value to: string.');
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
			throw new ConversionException('Failed to convert value to: int');
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
			throw new ConversionException('Failed to convert value to: float');
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
			throw new ConversionException('Failed to convert value to: bool');
		}

		$value = $new_value;
		return $value;
	}

	/**
	 * Convert a string represented array to an actual array
	 *
	 * @param mixed $value    The value to convert.
	 * @param array $metadata Any optional data that might be needed.
	 * @return array
	 */
	public static function convertToArray(mixed $value, array $metadata=[]): array
	{
		if (empty($value))
		{
			return [];
		}

		($metadata); // For removing the unused warning.

		if (is_string($value))
		{
			// Check for json
			$json_value = json_decode($value, true);
			if (json_last_error() === JSON_ERROR_NONE && !empty($json_value))
			{
				$value = $json_value;
				return $value;
			}

			// check for comma delimited string
			if (substr_count($value, ',') > 0)
			{
				$lines     = explode(PHP_EOL, $value);
				$csv_value = [];

				if (count($lines) === 1)
				{
					$csv_value = str_getcsv($lines[0]);
				}
				else
				{
					foreach ($lines as $line)
					{
						$csv_value[] = str_getcsv($line);
					}
				}

				$value = $csv_value;
				return $value;
			}
		}

		if (!is_array($value))
		{
			throw new ConversionException('Failed to convert value to: array');
		}

		return $value;
	}
}