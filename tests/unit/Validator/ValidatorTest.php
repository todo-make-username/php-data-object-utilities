<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\ObjectHelpers\Validator\Attributes\NotEmpty;
use TodoMakeUsername\ObjectHelpers\Validator\ObjectValidator;

/**
 * This one uses notEmpty to test since it is simple.
 */
class ValidatorTest extends TestCase
{
	public function testNoValidationNeeded()
	{
		$TestObj = new class()
		{
			public $prop1;
		};

		$Validator = new ObjectValidator($TestObj);
		$result = $Validator->isValid();
		$this->assertTrue($result);
	}

	public function testValidator()
	{
		$TestObj = new class()
		{
			#[NotEmpty]
			public $prop1;
		};

		$TestObj->prop1 = 'I have a value';

		$Validator = new ObjectValidator($TestObj);
		$result = $Validator->isValid();
		$this->assertTrue($result);
	}

	public function testValidatorAltObjectSet()
	{
		$TestObj = new class()
		{
			#[NotEmpty]
			public $prop1;
		};

		$TestObj->prop1 = 'I have a value';

		$Validator = new ObjectValidator();
		$Validator->setObject($TestObj);
		$result = $Validator->isValid();
		$this->assertTrue($result);
	}

	public function testValidatorGetObject()
	{
		$TestObj = new class()
		{
			#[NotEmpty]
			public $prop1 = 'I am a default';
		};

		$Validator = new ObjectValidator();
		$Validator->setObject($TestObj);
		$result = $Validator->isValid();
		$this->assertTrue($result);

		$Obj = $Validator->getObject();
		$this->assertSame($TestObj, $Obj);
	}

	public function testValidatorFail()
	{
		$TestObj = new class()
		{
			#[NotEmpty]
			public $prop1;
		};

		$Validator = new ObjectValidator($TestObj);
		$result = $Validator->isValid();
		$this->assertFalse($result);
	}

}