<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Hydrator\Attributes;

use Attribute;
use TodoMakeUsername\ObjectHelpers\Util\FilesHelper;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FileUpload implements HydratorAttributeInterface
{
	/**
	 * The File Upload Constructor
	 *
	 * TODO: Possibly make another parameter that takes a file class which can be used to create an object (or an array of them) to hydrate with the upload values.
	 *
	 * @param boolean $formatted_uploads This will format the multi-upload array into a cleaner array to work with. No effect on single files.
	 */
	public function __construct(public bool $formatted_uploads=false)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value, array $meta_data=[]): mixed
	{
		$Property      = $meta_data['Property'];
		$property_name = $Property->name;

		$value = FilesHelper::getRawFileData($property_name);

		if ($this->formatted_uploads) {
			$value = FilesHelper::squashMultiFileData($value);
		}

		return $value;
	}
}