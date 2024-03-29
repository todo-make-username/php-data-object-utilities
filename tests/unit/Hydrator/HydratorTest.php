<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataObjectUtilities\Attributes\Hydrator\HydratorSettings;
use TodoMakeUsername\DataObjectUtilities\Hydrator\ObjectHydrator;

class HydratorTest extends TestCase
{
	public function testBasicHydration()
	{
		$hydrate_data = [
			'field1' => 'test1',
			'field2' => 2,
		];

		$Obj = new class()
		{
			public $field1;
			public $field2;
		};
		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->assertSame($hydrate_data['field1'], $Obj->field1);
		$this->assertSame($hydrate_data['field2'], $Obj->field2);
	}

	public function testBasicHydrationSetObject()
	{
		$hydrate_data = [
			'field1' => true,
			'field2' => 23.5,
		];

		$Obj = new class()
		{
			public $field1;
			public $field2;
		};
		$Obj = (new ObjectHydrator())->setObject($Obj)->hydrate($hydrate_data)->getObject();

		$this->assertSame($hydrate_data['field1'], $Obj->field1);
		$this->assertSame($hydrate_data['field2'], $Obj->field2);
	}

	public function testBasicHydrationNoData()
	{
		$Obj = new class()
		{
			public $field1 = null;
			public $field2 = 1;
		};
		$Obj = (new ObjectHydrator($Obj))->hydrate([])->getObject();

		$this->assertSame(null, $Obj->field1);
		$this->assertSame(1, $Obj->field2);
	}

	public function testBasicHydrationExtraData()
	{
		$hydrate_data = [
			'field1' => 'test1',
			'field2' => 2,
			'field3' => 12.43,
			'field4' => false,
		];

		$Obj = new class()
		{
			public $field1;
			public $field2;
		};
		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->assertSame($hydrate_data['field1'], $Obj->field1);
		$this->assertSame($hydrate_data['field2'], $Obj->field2);
	}

	public function testHydrationObjectFail()
	{
		$Hydrator = new class() extends ObjectHydrator
		{
			protected function hydrateObjectProperty(object $Object, ReflectionProperty $Property): bool
			{
				return false;
			}
		};

		$Obj = new class()
		{
			public $field1;
			public $field2;
		};
		$Obj = $Hydrator->setObject($Obj)->hydrate([])->getObject();

		$this->assertSame(null, $Obj);
	}

	public function testHydrationSettings()
	{
		$hydrate_data = [
			'field1' => 'new value 1',
			'field2' => 'new value 2',
		];

		$Obj = new class()
		{
			#[HydratorSettings(hydrate: true)]
			public $field1 = 'old value 1';

			#[HydratorSettings(hydrate: false)]
			public $field2 = 'old value 2';
		};
		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->assertSame($hydrate_data['field1'], $Obj->field1);
		$this->assertSame('old value 2', $Obj->field2);
	}

}