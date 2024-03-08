<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataObjectUtilities\Util\StringHelper;

class StringHelperTest extends TestCase
{
	public static function stringHelperDataProvider()
	{
		return [
			[
				'String',
				'abc',
				true,
			],[
				'Int',
				'123',
				true,
			],[
				'Bool',
				true,
				true,
			],[
				'float',
				'123.123',
				true,
			],[
				'Object',
				new class() {},
				false,
			],[
				'Object w/ toString',
				new class()
				{
					public function __toString()
					{
						return 'I\'m a string!';
					}
				},
				true,
			]
		];
	}

	/**
	 * @dataProvider stringHelperDataProvider
	 */
	public function testIsStringCompatible(string $test_name, $value, bool $expected)
	{
		($test_name);
		$result = StringHelper::isStringCompatible($value);
		$this->assertSame($expected, $result);
	}
}