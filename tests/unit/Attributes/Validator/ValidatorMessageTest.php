<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\DataObjectUtilities\Attributes\Validator\NotEmpty;
use TodoMakeUsername\DataObjectUtilities\Attributes\Validator\ValidatorMessage;
use TodoMakeUsername\DataObjectUtilities\Validator\ObjectValidatorFailureException;
use TodoMakeUsername\DataObjectUtilities\Validator\ObjectValidator;
use TodoMakeUsername\DataObjectUtilities\Validator\ObjectValidatorException;

/**
 * This one uses notEmpty to test since it is simple.
 */
class ValidatorMessageTest extends TestCase
{
	public function testValidatorFailWithMessage()
	{
		$TestObj = new class()
		{
			#[NotEmpty]
			#[ValidatorMessage(attribute_class: NotEmpty::class, message: 'My fail message')]
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
			#[ValidatorMessage(attribute_class: NotEmpty::class, message: 'prop1 fail message')]
			public $prop1;

			#[NotEmpty]
			#[ValidatorMessage(NotEmpty::class, 'prop2 fail message')]
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
			#[ValidatorMessage(attribute_class: NotEmpty::class, message: 'My fail message', throw_exception: true)]
			public $prop1;
		};

		$this->expectException(ObjectValidatorFailureException::class);
		$this->expectExceptionMessage('My fail message');

		$Validator = new ObjectValidator($TestObj);
		$Validator->isValid();
		$this->fail('This should have errored out');
	}

	public function testValidatorBadAttributeClass()
	{
		$TestObj = new class()
		{
			#[NotEmpty]
			#[ValidatorMessage(attribute_class: DateTime::class, message: 'LOL wut')]
			public $prop1;
		};

		$this->expectException(ObjectValidatorException::class);
		$this->expectExceptionMessage("'DateTime' must extend the AbstractValidatorAttribute class to be used with ValidatorMessage");

		$Validator = new ObjectValidator($TestObj);
		$Validator->isValid();
		$this->fail('This should have errored out');
	}
}