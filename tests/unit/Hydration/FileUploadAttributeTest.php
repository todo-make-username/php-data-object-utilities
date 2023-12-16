<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TodoMakeUsername\ObjectHelpers\Hydrator\Attributes\FileUpload;
use TodoMakeUsername\ObjectHelpers\Hydrator\ObjectHydrator;

class FileUploadAttributeTest extends TestCase
{
	protected function tearDown(): void
	{
		$_FILES = [];
	}

	/**
	 * @dataProvider filesDataProvider
	 */
	public function testUploadAttributeFormatted(string $test_name, array $files, array $expected)
	{
		($test_name);
		$_FILES = [ 'field_name' => $files ];

		$Obj = new class()
		{
			#[FileUpload(formatted_uploads: true)]
			public array $field_name;
		};
		$Obj = (new ObjectHydrator($Obj))->hydrate([])->getObject();

		$this->assertSame($expected, $Obj->field_name, $test_name);
	}

	public static function filesDataProvider()
	{
		return [
			[
				'Single Upload',
				[
					"name"     => "tacos.png",
					"type"     => "image/png",
					"tmp_name" => "/tmp/tacoparty",
					"error"    => 0,
					"size"     => 1234
				],
				[
					"name"     => "tacos.png",
					"type"     => "image/png",
					"tmp_name" => "/tmp/tacoparty",
					"error"    => 0,
					"size"     => 1234
				],
			],
			[
				'Multi-Uploads',
				[
					"name" => [
						"apple.png",
						"pineapple.png",
						"",
					],
					"type" => [
						"image/png",
						"image/png",
						"",
					],
					"tmp_name" => [
						"/tmp/appletmp",
						"/tmp/pineappletmp",
						"",
					],
					"error" => [
						0,
						0,
						UPLOAD_ERR_NO_FILE,
					],
					"size" => [
						12345,
						54321,
						0,
					]
				],
				[
					[
						"name"     => "apple.png",
						"type"     => "image/png",
						"tmp_name" => "/tmp/appletmp",
						"error"    => 0,
						"size"     => 12345
					],
					[
						"name"     => "pineapple.png",
						"type"     => "image/png",
						"tmp_name" => "/tmp/pineappletmp",
						"error"    => 0,
						"size"     => 54321
					],
				],
			],
			[
				'Multi-Uploads Pre-Formatted',
				[
					[
						"name"     => "apple.png",
						"type"     => "image/png",
						"tmp_name" => "/tmp/appletmp",
						"error"    => 0,
						"size"     => 12345
					],
					[
						"name"     => "pineapple.png",
						"type"     => "image/png",
						"tmp_name" => "/tmp/pineappletmp",
						"error"    => 0,
						"size"     => 54321
					],
				],
				[
					[
						"name"     => "apple.png",
						"type"     => "image/png",
						"tmp_name" => "/tmp/appletmp",
						"error"    => 0,
						"size"     => 12345
					],
					[
						"name"     => "pineapple.png",
						"type"     => "image/png",
						"tmp_name" => "/tmp/pineappletmp",
						"error"    => 0,
						"size"     => 54321
					],
				],
			],
			[
				'Multi-Uploads Empty',
				[
					"name" => [
						"",
					],
					"type" => [
						"",
					],
					"tmp_name" => [
						"",
					],
					"error" => [
						UPLOAD_ERR_NO_FILE,
					],
					"size" => [
						0,
					]
				],
				[],
			],
			[
				'Somehow everything is empty',
				[],
				[],
			],
		];
	}
}