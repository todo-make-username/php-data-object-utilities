<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Hydrator;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use TodoMakeUsername\ObjectHelpers\Hydrator\Attributes\HydratorAttributeInterface;

class ObjectHydrator
{
	protected ReflectionClass $ReflectionClass;
	/**
	 * @var array{ReflectionProperty}
	 */
	protected array $ReflectiionProperties;
	protected object $Object;
	protected ?object $HydratedObject = null;
	protected array $hydrate_data;

	/**
	 * Set the object to be hydrated.
	 *
	 * @param object $Object The object to be hydrated.
	 * @return ObjectHydrator
	 */
	public function hydrate(object $Object): ObjectHydrator
	{
		$this->Object = $Object;
		return $this;
	}

	/**
	 * Set the data that will be used to hydrate the object.
	 *
	 * @param array $hydrate_data The data to hydrate the object with.
	 * @return ObjectHydrator
	 */
	public function with(array $hydrate_data): ObjectHydrator
	{
		$this->hydrate_data = $hydrate_data;
		return $this;
	}

	/**
	 * Get the hydrated object or null if failed.
	 *
	 * @return object|null
	 */
	public function getObject(): ?object
	{
		$this->HydratedObject = $this->hydrateObject(clone $this->Object);

		return $this->HydratedObject;
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

		// To use later.
		// $property_type = $Property->getType()?->getName();
		// $is_nullable   = $Property->getType()?->allowsNull() ?? true;

		// TODO: Make metaData object for hydration instead of array.
		$meta_data = [
			'Property' => $Property,
			'is_set'   => $is_set,
		];

		$ReflectionAttributes = $Property->getAttributes(HydratorAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF);
		foreach ($ReflectionAttributes as $ReflectionAttributes)
		{
			$Attribute = $ReflectionAttributes->newInstance();
			$value     = $Attribute->process($value, $meta_data);
		}

		if (!$is_set)
		{
			return true;
		}

		$Object->{$property_name} = $value;

		return true;
	}
}