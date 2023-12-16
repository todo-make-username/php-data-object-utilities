<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Hydrator\Attributes;

use Attribute;
use ReflectionProperty;
use TodoMakeUsername\ObjectHelpers\Util\FilesHelper;

/**
 * This takes the file array from the $_FILES array that matches the property name.
 *
 * Can only be used on duck, mixed, or array types.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class FileUpload implements HydratorAttributeInterface
{
	/**
	 * The reflection object of the property this attribute is on.
	 *
	 * @var ReflectionProperty
	 */
	public ReflectionProperty $Property;

	/**
	 * The File Upload Constructor
	 *
	 * TODO: Possibly make another parameter that takes a file class which can be used to create an object (or an array of them) to hydrate with the upload values.
	 *
	 * @param boolean $formatted_uploads This will format the multi-upload array into a cleaner array to work with. No effect on single files.
	 */
	public function __construct(protected bool $formatted_uploads=false)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		$Property      = $this->Property;
		$property_name = $Property->name;

		$value = FilesHelper::getRawFileData($property_name);

		if ($this->formatted_uploads)
		{
			$value = FilesHelper::formatMultiFileData($value);
		}

		return $value;
	}
}