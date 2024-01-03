<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpersDemo\Objects;

use TodoMakeUsername\ObjectHelpers\Attributes\Hydrator\FileUpload;

class UploadsObj implements ObjInterface
{
	#[FileUpload]
	public array $upload_single;

	#[FileUpload]
	public array $upload_multiple;

	#[FileUpload(formatted_uploads: true)]
	public array $upload_multiple_formatted;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'single'          => $this->upload_single,
			'multi'           => $this->upload_multiple,
			'multi formatted' => $this->upload_multiple_formatted,
		];
	}
}