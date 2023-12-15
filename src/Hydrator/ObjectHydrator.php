<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Hydrator;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use TodoMakeUsername\ObjectHelpers\Converter\Attributes\Conversion;
use TodoMakeUsername\ObjectHelpers\Converter\TypeConverter;
use TodoMakeUsername\ObjectHelpers\Helper\ObjectHelperInterface;
use TodoMakeUsername\ObjectHelpers\Hydrator\Attributes\HydratorAttributeInterface;

class ObjectHydrator implements ObjectHelperInterface
{
	protected ?object $Object;
	protected ?object $HydratedObject = null;
	protected array $hydrate_data;

	/**
	 * The constructor.
	 *
	 * @param object $Object The object to be hydrated [Optional].
	 */
	public function __construct(?object $Object=null)
	{
		if (is_null($Object))
		{
			return;
		}

		$this->setObject($Object);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setObject(object $Object): ObjectHelperInterface
	{
		$this->Object         = $Object;
		$this->HydratedObject = null;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getObject(): ?object
	{
		return $this->HydratedObject;
	}

	/**
	 * Hydrate the object.
	 *
	 * @param array $hydrate_data The data to hydrate the object with.
	 * @return ObjectHydrator
	 */
	public function hydrate(array $hydrate_data): ObjectHydrator
	{
		$this->hydrate_data   = $hydrate_data;
		$this->HydratedObject = $this->hydrateObject(clone $this->Object);
		return $this;
	}

	/**
	 * Hydrate the Object
	 *
	 * @param object $Object The object to hydrate.
	 * @return object|null Returns the hydrated object or null if failed.
	 */
	protected function hydrateObject(object $Object): ?object
	{
		$ReflectionClass       = new ReflectionClass($Object::class);
		$ReflectiionProperties = $ReflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

		foreach ($ReflectiionProperties as $Property)
		{
			if (!$this->hydrateObjectProperty($Object, $Property))
			{
				return null;
			}
		}

		return $Object;
	}

	/**
	 * Hydrate an object's property.
	 *
	 * @param object             $Object   The object with the property to hydrate.
	 * @param ReflectionProperty $Property The property Relection object.
	 * @return boolean
	 */
	protected function hydrateObjectProperty(object $Object, ReflectionProperty $Property): bool
	{
		$property_name = $Property->name;
		$value         = $this->hydrate_data[$property_name] ?? null;
		$is_set        = array_key_exists($property_name, $this->hydrate_data);

		// TODO: Make metaData object for hydration instead of array.
		$metadata = [
			'Property' => $Property,
			'is_set'   => $is_set,
		];

		$value = $this->processHydrationAttributes($Property, $value, $metadata);

		if (!$is_set && (($this->hydrate_data[$property_name] ?? null) === $value))
		{
			return true;
		}

		$property_type = $Property->getType()?->getName() ?? 'null';
		$value         = $this->convertValueToType($value, $property_type, $metadata);

		$Object->{$property_name} = $value;

		return true;
	}

	/**
	 * Process the Hydration attributes on a property
	 *
	 * @param ReflectionProperty $Property The property which might have hydration attributes.
	 * @param mixed              $value    The value that will be used to hydrate the object property.
	 * @param array              $metadata Any optional data that might be needed.
	 * @return mixed
	 */
	protected function processHydrationAttributes(ReflectionProperty $Property, mixed $value, array $metadata=[]): mixed
	{
		$ReflectionAttributes = $Property->getAttributes(HydratorAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF);

		foreach ($ReflectionAttributes as $ReflectionAttributes)
		{
			$Attribute = $ReflectionAttributes->newInstance();
			$value     = $Attribute->process($value, $metadata);
		}

		return $value;
	}

	/**
	 * Convert value to desired type.
	 *
	 * Only really works for basic data types.
	 *
	 * @param mixed  $value    The value to convert.
	 * @param string $type     The type to convert to.
	 * @param array  $metadata Any optional data that might be needed.
	 * @return mixed
	 */
	protected function convertValueToType(mixed $value, string $type, array $metadata=[]): mixed
	{
		$type = strtolower($type);

		$ConversionAttributes = $metadata['Property']?->getAttributes(Conversion::class, ReflectionAttribute::IS_INSTANCEOF) ?? [];
		$ConversionAttribute  = (count($ConversionAttributes) > 0) ? $ConversionAttributes[0]->newInstance() : (new Conversion());
		$metadata['strict']   = $ConversionAttribute->strict;

		return TypeConverter::convertTo($value, $type, $metadata);
	}
}