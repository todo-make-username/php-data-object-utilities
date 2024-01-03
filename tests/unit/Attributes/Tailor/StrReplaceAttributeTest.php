<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\ObjectHelpers\Attributes\Tailor\StrReplace;
use TodoMakeUsername\ObjectHelpers\Tailor\ObjectTailor;

class StrReplaceAttributeTest extends TestCase
{
	public function testStrReplace()
	{
		$Obj = new class()
		{
			#[StrReplace('R1', 'Hello')]
			public $field1 = 'R1 World!';
		};

		$Tailor = new ObjectTailor($Obj);
		$Obj    = $Tailor->tailor()->getObject();

		$this->assertSame('Hello World!', $Obj->field1);
	}

	public function testStrReplaceMultiple()
	{
		$Obj = new class()
		{
			#[StrReplace([ 'R1', 'R2', '?' ], [ 'Hello', 'World', '!'])]
			public $field1 = 'R1 R2?';
		};

		$Tailor = new ObjectTailor($Obj);
		$Obj    = $Tailor->tailor()->getObject();

		$this->assertSame('Hello World!', $Obj->field1);
	}

	public function testStrReplaceNotStringIgnored()
	{
		$Obj = new class()
		{
			#[StrReplace('R1', 'Hello')]
			public int $field1 = 123;
		};

		$Tailor = new ObjectTailor($Obj);
		$Obj    = $Tailor->tailor()->getObject();

		$this->assertSame(123, $Obj->field1);
	}
}