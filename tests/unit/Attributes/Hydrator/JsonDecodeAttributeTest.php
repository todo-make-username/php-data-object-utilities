<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataObjectUtilities\Attributes\Hydrator\JsonDecode;
use TodoMakeUsername\DataObjectUtilities\Hydrator\ObjectHydrationException;
use TodoMakeUsername\DataObjectUtilities\Hydrator\ObjectHydrator;

class JsonDecodeAttributeTest extends TestCase
{
	/**
	 * @dataProvider arrayDataProvider
	 */
	public function testJsonConversionSuccess(string $test_name, mixed $test_data, mixed $expected)
	{
		$hydrate_data = [
			'json' => $test_data,
		];

		$Obj = new class()
		{
			#[JsonDecode(true)]
			public array $json;
		};
		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->assertSame($expected, $Obj->json, $test_name);
	}

	public static function arrayDataProvider()
	{
		return [
			[
				'json',
				'{"A":"1","B":2}',
				[ 'A' => '1', 'B' => 2 ],
			],
			[
				'object with __toString',
				(new class() { public function __toString(){ return '{"A": { "C": "d"} ,"B": [ 1, 2, 3 ]}'; }}),
				[ 'A' => [ 'C' => 'd' ], 'B' => [ 1, 2, 3 ] ],
			],
			[
				'already an array',
				[],
				[],
			],
			[
				'empty, but conversion takes over',
				'',
				[],
			],
		];
	}

	public function testIncompatibleTypeFail()
	{
		$hydrate_data = [
			'field1' => (new DateTime()),
		];

		$Obj = new class()
		{
			#[JsonDecode(true)]
			public array $field1;
		};

		$this->expectException(ObjectHydrationException::class);
		$this->expectExceptionMessage("'field1' requires an array or string compatible value.");

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->fail();
	}

	public function testBadJson()
	{
		$hydrate_data = [
			'field1' => '{"A":"1","B":2}}}}}}}}}}}}}}}}}',
		];

		$Obj = new class()
		{
			#[JsonDecode(true)]
			public array $field1;
		};

		$this->expectException(ObjectHydrationException::class);
		$this->expectExceptionMessage("Failed to hydrate 'field1', a valid JSON string is required.");

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->fail();
	}
}