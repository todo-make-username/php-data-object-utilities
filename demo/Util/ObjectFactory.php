<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilitiesDemo\Util;

class ObjectFactory
{
	/**
	 * A quick and dirty way to dynamically create the test objects. Returns null if not found.
	 *
	 * @param string $section The section the ojbect is for.
	 * @return object|null
	 */
	public static function create(string $section): ?object
	{
		$section    = ucwords($section, ' _');
		$section    = str_replace('_', '', $section);
		$class_name = 'TodoMakeUsername\\DataObjectUtilitiesDemo\\Objects\\'.$section.'Obj';
		if (class_exists($class_name))
		{
			return new $class_name();
		}

		return null;
	}
}