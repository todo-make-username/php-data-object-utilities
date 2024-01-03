<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\ObjectHelpers\ObjectHelper;
use TodoMakeUsername\ObjectHelpers\Attributes\Tailor\Trim;
use TodoMakeUsername\ObjectHelpers\Attributes\Validator\NotEmpty;
use TodoMakeUsername\ObjectHelpers\Attributes\Validator\ValidatorMessage;

/**
 * This uses notEmpty and trim to test since they are simple.
 */
class ObjectHelperTest extends TestCase
{
	protected $TestObj;

	protected function setUp(): void
	{
		$this->TestObj = (new class() {
			#[Trim]
			#[NotEmpty]
			#[ValidatorMessage(attribute_class: NotEmpty::class, message: 'Must not be empty')]
			public $prop1;
		});
	}

	public function testObjectHelperAltObjLoad()
	{
		$Helper = (new ObjectHelper())->setObject($this->TestObj);

		$test_value = '  test  ';
		$Obj = $Helper->hydrate([ 'prop1' => $test_value ])->getObject();
		$this->assertSame($test_value, $Obj->prop1);

		$Obj = $Helper->tailor()->getObject();
		$this->assertSame('test', $Obj->prop1);

		$is_valid = $Helper->isValid();
		$this->assertTrue($is_valid);
		$this->assertEmpty($Helper->getValidatorMessages());
	}

	public function testObjectHelperAll()
	{
		$Helper = new ObjectHelper($this->TestObj);

		$test_value = '  test  ';
		$Obj = $Helper->hydrate([ 'prop1' => $test_value ])->getObject();
		$this->assertSame($test_value, $Obj->prop1);

		$Obj = $Helper->tailor()->getObject();
		$this->assertSame('test', $Obj->prop1);

		$is_valid = $Helper->isValid();
		$this->assertTrue($is_valid);
		$this->assertEmpty($Helper->getValidatorMessages());
	}

	public function testObjectHelperValidationFail()
	{
		$Helper = new ObjectHelper($this->TestObj);

		$test_value = '  ';
		$Obj = $Helper->hydrate([ 'prop1' => $test_value ])->getObject();
		$this->assertSame($test_value, $Obj->prop1);

		$Obj = $Helper->tailor()->getObject();
		$this->assertSame('', $Obj->prop1);

		$is_valid = $Helper->isValid();
		$messages = $Helper->getValidatorMessages();
		$this->assertFalse($is_valid);
		$this->assertCount(1, $messages);
		$this->assertSame('Must not be empty', $messages[0]);
	}
}