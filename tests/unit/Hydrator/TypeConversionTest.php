<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataObjectUtilities\Attributes\Converter\ConversionSettings;
use TodoMakeUsername\DataObjectUtilities\Attributes\Hydrator\HydratorSettings;
use TodoMakeUsername\DataObjectUtilities\Converter\ConversionException;
use TodoMakeUsername\DataObjectUtilities\Hydrator\ObjectHydrator;

class TypeConversionTest extends TestCase
{
	public function testNoConvertFailure()
	{
		$hydrate_data = [
			'from_int'    => 321,
		];

		$Obj = new class()
		{
			#[HydratorSettings(convert: false)]
			public string $from_int;
		};

		$this->expectException(TypeError::class);
		$this->expectExceptionMessage('Cannot assign int to property class@anonymous::$from_int of type string');

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();
	}

	public function testObjectConversion()
	{
		$hydrate_data = [
			'to_string' => (new class() { public function __toString(){ return 'test'; }}),
			'to_bool'   => (new class() { public function __toString(){ return 'yes'; }}),
			'to_int'    => (new class() { public function __toString(){ return '321'; }}),
		];

		$expected = [
			'to_string' => 'test',
			'to_bool'   => true,
			'to_int'    => 321,
		];

		$Obj = new class()
		{
			public string $to_string;
			public bool   $to_bool;
			public int    $to_int;
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		foreach (array_keys($expected) as $field)
		{
			$this->assertSame($expected[$field], $Obj->{$field}, $field);
		}
	}

	/**
	 * Int
	 */
	public function testIntConversion()
	{
		$hydrate_data = [
			'from_string' => '123',
			'from_bool'   => true,
			'from_int'    => 321,
		];

		$expected = [
			'from_string' => 123,
			'from_bool'   => 1,
			'from_int'    => 321,
		];

		$Obj = new class()
		{
			public int $from_string;
			public int $from_bool;
			public int $from_int;
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		foreach (array_keys($expected) as $field)
		{
			$this->assertSame($expected[$field], $Obj->{$field}, $field);
		}
	}

	public function testIntConversionFail()
	{
		$hydrate_data = [
			'from_float' => '123.123',
		];

		$Obj = new class()
		{
			public int $from_float;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert string to int');

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->fail('This was supposed to fail.');
	}

	public function testIntConversionNotStrict()
	{
		$hydrate_data = [
			'from_string' => '123aa',
		];

		$Obj = new class()
		{
			#[ConversionSettings(strict: false)]
			public int $from_string;
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->assertSame(123, $Obj->from_string);
	}

	/**
	 * Bool
	 */
	public function testBoolConversion()
	{
		$hydrate_data = [
			'from_string' => 'on',
			'from_bool'   => true,
			'from_int'    => 0,
		];

		$expected = [
			'from_string' => true,
			'from_bool'   => true,
			'from_int'    => false,
		];

		$Obj = new class()
		{
			public bool $from_string;
			public bool $from_bool;
			public bool $from_int;
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		foreach (array_keys($expected) as $field)
		{
			$this->assertSame($expected[$field], $Obj->{$field}, $field);
		}
	}

	public function testFancyBoolConversion()
	{
		$hydrate_data = [
			'from_on'    => 'on',
			'from_off'   => 'OfF',
			'from_yes'   => 'YES',
			'from_no'    => 'nO',
			'from_1'     => '1',
			'from_0'     => '0',
			'from_true'  => 'TrUe',
			'from_false' => 'fAlSe',
		];

		$expected = [
			'from_on'    => true,
			'from_off'   => false,
			'from_yes'   => true,
			'from_no'    => false,
			'from_1'     => true,
			'from_0'     => false,
			'from_true'  => true,
			'from_false' => false,
		];

		$Obj = new class()
		{
			public bool $from_on;
			public bool $from_off;
			public bool $from_yes;
			public bool $from_no;
			public bool $from_1;
			public bool $from_0;
			public bool $from_true;
			public bool $from_false;
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		foreach (array_keys($expected) as $field)
		{
			$this->assertSame($expected[$field], $Obj->{$field}, $field);
		}
	}

	public function testBoolConversionFail()
	{
		$hydrate_data = [
			'from_float' => '123.123',
		];

		$Obj = new class()
		{
			public bool $from_float;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert string to bool');

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->fail('This was supposed to fail.');
	}

	public function testBoolConversionNotStrict()
	{
		$hydrate_data = [
			'from_string' => '123aa',
		];

		$Obj = new class()
		{
			#[ConversionSettings(strict: false)]
			public bool $from_string;
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->assertSame(true, $Obj->from_string);
	}

	/**
	 * Float
	 */
	public function testFloatConversion()
	{
		$hydrate_data = [
			'from_string' => '123.123',
			'from_bool'   => true,
			'from_int'    => 321,
		];

		$expected = [
			'from_string' => 123.123,
			'from_bool'   => 1.0,
			'from_int'    => 321.0,
		];

		$Obj = new class()
		{
			public float $from_string;
			public float $from_bool;
			public float $from_int;
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		foreach (array_keys($expected) as $field)
		{
			$this->assertSame($expected[$field], $Obj->{$field}, $field);
		}
	}

	public function testFloatConversionFail()
	{
		$hydrate_data = [
			'from_array' => [],
		];

		$Obj = new class()
		{
			public float $from_array;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert array to float');

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->fail('This was supposed to fail.');
	}

	public function testFloatConversionNotStrict()
	{
		$hydrate_data = [
			'from_string' => '123.321aa',
		];

		$Obj = new class()
		{
			#[ConversionSettings(strict: false)]
			public float $from_string;
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->assertSame(123.321, $Obj->from_string);
	}

	/**
	 * String
	 */
	public function testStringConversion()
	{
		$hydrate_data = [
			'from_string' => '123.123',
			'from_bool'   => true,
			'from_int'    => 321,
		];

		$expected = [
			'from_string' => '123.123',
			'from_bool'   => '1',
			'from_int'    => '321',
		];

		$Obj = new class()
		{
			public string $from_string;
			public string $from_bool;
			public string $from_int;
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		foreach (array_keys($expected) as $field)
		{
			$this->assertSame($expected[$field], $Obj->{$field}, $field);
		}
	}

	public function testStringConversionFail()
	{
		$hydrate_data = [
			'from_obj' => (new DateTime()),
		];

		$Obj = new class()
		{
			public string $from_obj;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert object to string');

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->fail('This was supposed to fail.');
	}

	/**
	 * Mixed
	 */
	public function testMixedConversion()
	{
		$hydrate_data = [
			'from_string' => 'abc123',
			'from_bool'   => true,
			'from_int'    => 321,
			'from_float'  => 123.123,
			'from_array'  => [1,2,3],
		];

		$Obj = new class()
		{
			public $from_string;
			public $from_bool;
			public $from_int;
			public $from_float;
			public $from_array;
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		foreach (array_keys($hydrate_data) as $field)
		{
			$this->assertSame($hydrate_data[$field], $Obj->{$field}, $field);
		}
	}

	/**
	 * Arrays
	 */
	public function testEmptyArrayConversionSuccess()
	{
		$hydrate_data = [
			'field1' => '',
			'field2' => null,
		];

		$Obj = new class()
		{
			public array $field1;
			public array $field2;
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->assertSame([], $Obj->field1);
		$this->assertSame([], $Obj->field2);
	}

	public function testArrayConversionFail()
	{
		$hydrate_data = [
			'field1' => 'not empty',
		];

		$Obj = new class()
		{
			public array $field1;
		};

		$this->expectException(ConversionException::class);
		$this->expectExceptionMessage('Failed to convert string to array.');

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->fail();
	}
}