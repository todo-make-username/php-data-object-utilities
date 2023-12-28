<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\ObjectHelpers\Validator\Attributes\RegexMatch;
use TodoMakeUsername\ObjectHelpers\Validator\ObjectValidator;
use TodoMakeUsername\ObjectHelpers\Validator\ObjectValidatorException;

class RegexMatchAttributeTest extends TestCase
{
	public function testRegexValidateTrue()
	{
		$TestObj = new class()
		{
			#[RegexMatch(pattern: '/^[A-Z]+$/')]
			public $prop1 = 'ABC';
		};

		$result = (new ObjectValidator($TestObj))->isValid();
		$this->assertTrue($result);
	}

	public function testRegexValidateFalse()
	{
		$TestObj = new class()
		{
			#[RegexMatch(pattern: '/^[A-Z]+$/')]
			public $prop1 = 'abc';
		};

		$result = (new ObjectValidator($TestObj))->isValid();
		$this->assertFalse($result);
	}

	public function testRegexValidateFalseUnitializedNoType()
	{
		$TestObj = new class()
		{
			#[RegexMatch(pattern: '/^[A-Z]+$/')]
			public $prop1;
		};

		$result = (new ObjectValidator($TestObj))->isValid();
		$this->assertFalse($result);
	}

	public function testRegexValidateFalseUnitializedTyped()
	{
		$TestObj = new class()
		{
			#[RegexMatch(pattern: '/^[01]+$/')]
			public int $prop1;
		};

		$result = (new ObjectValidator($TestObj))->isValid();
		$this->assertFalse($result);
	}

	public function testRegexValidateIntPatternTrue()
	{
		$TestObj = new class()
		{
			#[RegexMatch(pattern: '/^[10]+$/')] // Binary numbers only
			public int $prop1 = 10010010101;
		};

		$result = (new ObjectValidator($TestObj))->isValid();
		$this->assertTrue($result);
	}

	public function testRegexValidateInvalidPattern()
	{
		$TestObj = new class()
		{
			#[RegexMatch(pattern: '/^([0-9]++/')]
			public $prop1 = '0000000000000';
		};

		$this->expectException(ObjectValidatorException::class);
		$this->expectExceptionMessage("Invalid pattern used to validate 'prop1': '/^([0-9]++/'");
		(new ObjectValidator($TestObj))->isValid();
		$this->fail('This should have errored out');
	}

	public function testRegexValidateNotStringCompatible()
	{
		$TestObj = new class()
		{
			#[RegexMatch(pattern: '/^[A-Z]+$/')]
			public $prop1;
		};

		$TestObj->prop1 = (new class() {});

		$result = (new ObjectValidator($TestObj))->isValid();
		$this->assertFalse($result);
	}
}