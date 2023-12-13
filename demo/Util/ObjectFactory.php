<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpersDemo\Util;

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
		$class_name = 'TodoMakeUsername\\ObjectHelpersDemo\\Objects\\'.$section.'Obj';
		if (class_exists($class_name))
		{
			return new $class_name();
		}

		return null;
	}
}