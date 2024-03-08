<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataObjectUtilities\Hydrator\ObjectHydrator;
use TodoMakeUsername\DataObjectUtilities\Attributes\Tailor\Trim;
use TodoMakeUsername\DataObjectUtilities\Attributes\Tailor\UseDefaultOnEmpty;
use TodoMakeUsername\DataObjectUtilities\Tailor\ObjectTailor;
use TodoMakeUsername\DataObjectUtilities\Tailor\ObjectTailoringException;

class UseDefaultOnEmptyAttributeTest extends TestCase
{
	public function testUseDefaultOnEmpty()
	{
		$Obj = new class()
		{
			#[UseDefaultOnEmpty]
			public $field1 = 'default';
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate([ 'field1' => '' ])->getObject();
		$Obj = (new ObjectTailor($Obj))->tailor()->getObject();

		$this->assertSame('default', $Obj->field1);
	}

	public function testTrimWithUseDefaultOnEmpty()
	{
		$Obj = new class()
		{
			#[Trim]
			#[UseDefaultOnEmpty]
			public $field1 = 'default';
		};

		$Obj = (new ObjectHydrator($Obj))->hydrate([ 'field1' => '            ' ])->getObject();
		$Obj = (new ObjectTailor($Obj))->tailor()->getObject();

		$this->assertSame('default', $Obj->field1);
	}

	public function testUseDefaultOnEmptyNoDefault()
	{
		$Obj = new class()
		{
			#[UseDefaultOnEmpty]
			public string $field1;
		};

		$this->expectException(ObjectTailoringException::class);
		$this->expectExceptionMessage('The property: "field1" must have a default value for the DefaultOnEmpty attribute.');

		$Obj = (new ObjectHydrator($Obj))->hydrate([ 'field1' => '' ])->getObject();
		$Obj = (new ObjectTailor($Obj))->tailor()->getObject();

		$this->fail('This should have failed.');
	}
}