<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilities\Hydrator;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use TodoMakeUsername\DataObjectUtilities\Converter\TypeConverter;
use TodoMakeUsername\DataObjectUtilities\Shared\ObjectHelperInterface;
use TodoMakeUsername\DataObjectUtilities\Attributes\Converter\ConversionSettings;
use TodoMakeUsername\DataObjectUtilities\Attributes\Hydrator\AbstractHydratorAttribute;
use TodoMakeUsername\DataObjectUtilities\Attributes\Hydrator\HydratorSettings;
use TodoMakeUsername\DataObjectUtilities\Attributes\Shared\ObjectHelperAttributeInterface;

class ObjectHydrator implements ObjectHelperInterface
{
	protected ?object $Object;
	protected ?object $HydratedObject = null;
	protected array   $hydrate_data;

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
	public function setObject(object $Object): ObjectHydrator
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
	 * @return self
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
		$ReflectionClass      = new ReflectionClass($Object::class);
		$ReflectionProperties = $ReflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

		foreach ($ReflectionProperties as $Property)
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
	 * @param ReflectionProperty $Property The property Reflection object.
	 * @return boolean Return false on error.
	 */
	protected function hydrateObjectProperty(object $Object, ReflectionProperty $Property): bool
	{
		$property_name = $Property->name;
		$value         = $this->hydrate_data[$property_name] ?? null;
		$is_set        = array_key_exists($property_name, $this->hydrate_data);

		// This metadata is used to hydrate Hydration attributes.
		$metadata = [
			'Property' => $Property,
			'is_set'   => $is_set,
		];

		$HydrationSettings = $this->getHydrationSettingsAttribute($Property);

		// Skip Hydrating if hydration is set to false.
		if (!$HydrationSettings->hydrate)
		{
			return true;
		}

		// We don't recursively hydrate attributes on attributes from this project to avoid an infinite loop.
		// There shouldn't be any attributes on attribute properties, but just in case.
		if (!($Object instanceof ObjectHelperAttributeInterface))
		{
			$value = $this->processHydrationAttributes($Property, $value, $metadata);
		}

		// If the value wasn't passed in, and the hydration didn't change the value, just move on.
		if (!$is_set && (($this->hydrate_data[$property_name] ?? null) === $value))
		{
			return true;
		}

		if ($HydrationSettings->convert)
		{
			$ConversionAttribute = $this->getConversionSettingsAttribute($Property);
			$metadata['strict']  = $ConversionAttribute->strict;
			$property_type       = $Property->getType()?->getName() ?? 'null';
			$value               = $this->convertValueToType($value, $property_type, $metadata);
		}

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
		$ReflectionAttributes = $Property->getAttributes(AbstractHydratorAttribute::class, ReflectionAttribute::IS_INSTANCEOF);
		$Hydrator             = new ObjectHydrator();

		foreach ($ReflectionAttributes as $ReflectionAttribute)
		{
			$Attribute = $ReflectionAttribute->newInstance();
			$Attribute = $Hydrator->setObject($Attribute)->hydrate($metadata)->getObject();
			$value     = $Attribute->process($value);
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

		return TypeConverter::convertTo($value, $type, $metadata);
	}

	/**
	 * Get the conversion settings attribute.
	 *
	 * @param ReflectionProperty $Property The property to get the settings for.
	 * @return ConversionSettings
	 */
	protected function getConversionSettingsAttribute(ReflectionProperty $Property): ConversionSettings
	{
		$ConversionAttributes = $Property->getAttributes(ConversionSettings::class, ReflectionAttribute::IS_INSTANCEOF) ?? [];
		$ConversionAttribute  = (count($ConversionAttributes) > 0) ? $ConversionAttributes[0]->newInstance() : (new ConversionSettings());

		return $ConversionAttribute;
	}

	/**
	 * Get the hydration settings attribute.
	 *
	 * @param ReflectionProperty $Property The property to get the settings for.
	 * @return HydratorSettings
	 */
	protected function getHydrationSettingsAttribute(ReflectionProperty $Property): HydratorSettings
	{
		$HydrationAttributes = $Property->getAttributes(HydratorSettings::class, ReflectionAttribute::IS_INSTANCEOF) ?? [];
		$HydrationAttribute  = (count($HydrationAttributes) > 0) ? $HydrationAttributes[0]->newInstance() : (new HydratorSettings());

		return $HydrationAttribute;
	}
}