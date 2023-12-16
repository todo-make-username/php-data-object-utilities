<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\ObjectHelpers\Hydrator\Attributes\Required;
use TodoMakeUsername\ObjectHelpers\Hydrator\ObjectHydrationException;
use TodoMakeUsername\ObjectHelpers\Hydrator\ObjectHydrator;

class RequiredAttributeTest extends TestCase
{
	public function testBasicRequired()
	{
		$hydrate_data = [
			'field1' => 'test1',
			'field2' => 2,
		];

		$Obj = new class()
		{
			public $field1;

			#[Required]
			public $field2;
		};
		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->assertSame($hydrate_data['field1'], $Obj->field1);
		$this->assertSame($hydrate_data['field2'], $Obj->field2);
	}

	public function testBasicRequiredMissing()
	{
		$hydrate_data = [
			'field1' => 'test1',
		];

		$Obj = new class()
		{
			public $field1;

			#[Required]
			public $field2;
		};

		$this->expectException(ObjectHydrationException::class);
		$this->expectExceptionMessage("A value is required for 'field2'.");

		$Obj = (new ObjectHydrator($Obj))->hydrate($hydrate_data)->getObject();

		$this->fail('This should have failed.');
	}
}