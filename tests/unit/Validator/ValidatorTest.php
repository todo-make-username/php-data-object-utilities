<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\ObjectHelpers\Validator\Attributes\NotEmpty;
use TodoMakeUsername\ObjectHelpers\Validator\Attributes\ValidationMessage;
use TodoMakeUsername\ObjectHelpers\Validator\ObjectValidationFailureException;
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
		$result = $Validator->validate();
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

	public function testValidatorFailWithMessage()
	{
		$TestObj = new class()
		{
			#[NotEmpty]
			#[ValidationMessage(attribute_class: NotEmpty::class, message: 'My fail message')]
			public $prop1;
		};

		$Validator = new ObjectValidator($TestObj);
		$result = $Validator->isValid();
		$this->assertFalse($result);

		$last_fail_message = $Validator->getMessage();
		$this->assertSame('My fail message', $last_fail_message);
	}

	public function testValidatorMultipleFailWithMultipleMessages()
	{
		$TestObj = new class()
		{
			#[NotEmpty]
			#[ValidationMessage(attribute_class: NotEmpty::class, message: 'prop1 fail message')]
			public $prop1;

			#[NotEmpty]
			#[ValidationMessage(NotEmpty::class, 'prop2 fail message')]
			public $prop2;
		};

		$Validator = new ObjectValidator($TestObj);
		$result = $Validator->isValid();
		$this->assertFalse($result);

		$messages = $Validator->getMessages();
		$this->assertTrue(in_array('prop1 fail message', $messages));
		$this->assertTrue(in_array('prop2 fail message', $messages));
		$this->assertCount(2, $messages);
	}

	public function testValidatorFailWithExceptionMessage()
	{
		$TestObj = new class()
		{
			#[NotEmpty]
			#[ValidationMessage(attribute_class: NotEmpty::class, message: 'My fail message', throw_exception: true)]
			public $prop1;
		};

		$this->expectException(ObjectValidationFailureException::class);
		$this->expectExceptionMessage('My fail message');

		$Validator = new ObjectValidator($TestObj);
		$result = $Validator->isValid();
	}
}