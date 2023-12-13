<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpersDemo\Util;

class ObjectFactory
{
	public static function create(string $section): ?object
	{
		$section = ucwords($section, ' _');
		$class_name = 'TodoMakeUsername\\ObjectHelpersDemo\\Objects\\' . $section . 'Obj';
		if (class_exists($class_name))
		{
			return new $class_name();
		}

		return null;
	} 
}