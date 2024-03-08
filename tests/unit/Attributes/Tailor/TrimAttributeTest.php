<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataObjectUtilities\Attributes\Tailor\Trim;
use TodoMakeUsername\DataObjectUtilities\Tailor\ObjectTailor;

class TrimAttributeTest extends TestCase
{
	public function testTrim()
	{
		$Obj = new class()
		{
			#[Trim]
			public $field1 = '  abc ';
		};

		$Tailor = new ObjectTailor($Obj);
		$Obj    = $Tailor->tailor()->getObject();

		$this->assertSame('abc', $Obj->field1);
	}

	public function testNotStringIgnored()
	{
		$Obj = new class()
		{
			#[Trim]
			public int $field1 = 123;
		};

		$Tailor = new ObjectTailor($Obj);
		$Obj    = $Tailor->tailor()->getObject();

		$this->assertSame(123, $Obj->field1);
	}

	public function testTrimOtherCharacters()
	{
		$Obj = new class()
		{
			#[Trim('!')]
			public string $field1 = '!Hello World!!';
		};

		$Tailor = new ObjectTailor($Obj);
		$Obj    = $Tailor->tailor()->getObject();

		$this->assertSame('Hello World', $Obj->field1);
	}
}