<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Tailor;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use TodoMakeUsername\ObjectHelpers\Shared\Attributes\ObjectHelperAttributeInterface;
use TodoMakeUsername\ObjectHelpers\Shared\ObjectHelperInterface;
use TodoMakeUsername\ObjectHelpers\Hydrator\ObjectHydrator;
use TodoMakeUsername\ObjectHelpers\Tailor\Attributes\TailorAttributeInterface;

/**
 * This class uses property attributes to alter (tailor) the data stored in public properties.
 */
class ObjectTailor implements ObjectHelperInterface
{
	protected ?object $Object;
	protected ?object $AlteredObject = null;

	/**
	 * The constructor.
	 *
	 * @param object $Object The object to be processed [Optional].
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
	public function setObject(object $Object): ObjectTailor
	{
		$this->Object        = $Object;
		$this->AlteredObject = null;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getObject(): ?object
	{
		return $this->AlteredObject;
	}

	/**
	 * Process the tailor attributes on an object's public properties.
	 *
	 * @return ObjectTailor
	 */
	public function tailor(): ObjectTailor
	{
		$this->AlteredObject = $this->tailorObject(clone $this->Object);
		return $this;
	}

	/**
	 * Tailor the Object's properties.
	 *
	 * @param object $Object The object to tailor.
	 * @return object|null Returns the tailored object or null if failed.
	 */
	protected function tailorObject(object $Object): ?object
	{
		$ReflectionClass       = new ReflectionClass($Object::class);
		$ReflectiionProperties = $ReflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

		foreach ($ReflectiionProperties as $Property)
		{
			if (!$this->tailorObjectProperty($Object, $Property))
			{
				return null;
			}
		}

		return $Object;
	}

	/**
	 * Tailor an object's property.
	 *
	 * @param object             $Object   The object with the property to tailor.
	 * @param ReflectionProperty $Property The property Relection object.
	 * @return boolean Return false on error.
	 */
	protected function tailorObjectProperty(object $Object, ReflectionProperty $Property): bool
	{
		$property_name  = $Property->name;
		$is_initialized = $Property->isInitialized($Object);
		$initial_value  = ($is_initialized) ? $Object->{$property_name} : $Property->getDefaultValue();
		$value          = null;

		// This metadata is used to hydrate Tailor attributes.
		$metadata = [
			'Property'       => $Property,
			'is_initialized' => $is_initialized,
		];

		// We don't recursively call attributes on attributes from this project to avoid an infinite loop.
		// There shouldn't be any attributes on attribute properties, but just in case.
		if (!($Object instanceof ObjectHelperAttributeInterface))
		{
			$value = $this->processTailorAttributes($Property, $initial_value, $metadata);
		}

		// This is here so we don't overwrite unitialized properties.
		if ($initial_value === $value)
		{
			return true;
		}

		$Object->{$property_name} = $value;

		return true;
	}

	/**
	 * Process the Tailor attributes on a property.
	 *
	 * It processes attributes in order from top to bottom.
	 *
	 * @param ReflectionProperty $Property The property which might have tailor attributes.
	 * @param mixed              $value    The value that will be modified by the attributes.
	 * @param array              $metadata Any optional data that might be needed.
	 * @return mixed
	 */
	protected function processTailorAttributes(ReflectionProperty $Property, mixed $value, array $metadata=[]): mixed
	{
		$ReflectionAttributes = $Property->getAttributes(TailorAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF);
		$Hydrator             = new ObjectHydrator();

		foreach ($ReflectionAttributes as $ReflectionAttributes)
		{
			$Attribute = $ReflectionAttributes->newInstance();
			$Attribute = $Hydrator->setObject($Attribute)->hydrate($metadata)->getObject();
			$value     = $Attribute->process($value);
		}

		return $value;
	}
}