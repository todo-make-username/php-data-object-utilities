<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\ObjectHelpers\Validator\Attributes\NotEmpty;
use TodoMakeUsername\ObjectHelpers\Validator\ObjectValidator;

class NotEmptyAttributeTest extends TestCase
{
	public function testNotEmptyValidateTrue()
	{
		$TestObj = new class()
		{
			#[NotEmpty]
			public $prop1;
		};

		$TestObj->prop1 = 'No longer empty';

		$result = (new ObjectValidator($TestObj))->isValid();
		$this->assertTrue($result);
	}

	public function testNotEmptyValidateFalse()
	{
		$TestObj = new class()
		{
			#[NotEmpty]
			public $prop1;
		};

		$TestObj->prop1 = null;

		$result = (new ObjectValidator($TestObj))->isValid();
		$this->assertFalse($result);
	}

}