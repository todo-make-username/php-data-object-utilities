<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Util;

class StringHelper
{
	/**
	 * Find out if a value can be used as a string.
	 *
	 * Object that have __toString will pass this check.
	 *
	 * @param mixed $value The value to check.
	 * @return boolean
	 */
	public static function isStringCompatible(mixed $value): bool
	{
		try
		{
			strval($value);
		}
		catch (\Throwable $th)
		{
			return false;
		}

		return true;
	}
}