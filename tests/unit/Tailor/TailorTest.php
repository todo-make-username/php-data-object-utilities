<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\ObjectHelpers\Attributes\Tailor\Trim;
use TodoMakeUsername\ObjectHelpers\Tailor\ObjectTailor;

/**
 * This one uses trim to test since it is simple.
 */
class TailorTest extends TestCase
{
	public function testNoTailoringNeeded()
	{
		$Obj = new class()
		{
			public $field1 = 'abc';
		};

		$Tailor = new ObjectTailor($Obj);
		$Obj    = $Tailor->tailor()->getObject();

		$this->assertSame('abc', $Obj->field1);
	}

	public function testTailoring()
	{
		$Obj = new class()
		{
			#[Trim]
			public $field1 = '  abc  ';
		};

		$Tailor = new ObjectTailor($Obj);
		$Obj    = $Tailor->tailor()->getObject();

		$this->assertSame('abc', $Obj->field1);
	}

	public function testTailoringSetMethod()
	{
		$Obj = new class()
		{
			#[Trim]
			public $field1 = '  abc  ';
		};

		$Tailor = new ObjectTailor();
		$Obj    = $Tailor->setObject($Obj)->tailor()->getObject();

		$this->assertSame('abc', $Obj->field1);
	}

	public function testTailorObjectFail()
	{
		$Tailor = new class() extends ObjectTailor
		{
			protected function tailorObjectProperty(object $Object, ReflectionProperty $Property): bool
			{
				return false;
			}
		};

		$Obj = new class()
		{
			public $field1;
			public $field2;
		};
		$Obj = $Tailor->setObject($Obj)->tailor()->getObject();

		$this->assertSame(null, $Obj);
	}
}