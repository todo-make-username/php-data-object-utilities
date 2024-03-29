<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilities\Validator;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use TodoMakeUsername\DataObjectUtilities\Hydrator\ObjectHydrator;
use TodoMakeUsername\DataObjectUtilities\Shared\ObjectHelperInterface;
use TodoMakeUsername\DataObjectUtilities\Attributes\Shared\ObjectHelperAttributeInterface;
use TodoMakeUsername\DataObjectUtilities\Attributes\Validator\AbstractValidatorAttribute;
use TodoMakeUsername\DataObjectUtilities\Attributes\Validator\ValidatorMessage;

/**
 * This class uses attributes to validate properties. No values are altered.
 */
class ObjectValidator implements ObjectHelperInterface
{
	protected ?object $Object;
	protected bool    $is_valid = false;
	protected array   $messages = [];

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
	public function setObject(object $Object): ObjectValidator
	{
		$this->Object = $Object;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getObject(): ?object
	{
		return $this->Object;
	}

	/**
	 * Get the last validation message.
	 *
	 * @return string
	 */
	public function getMessage(): string
	{
		$number_of_messages = count($this->messages);
		return ($number_of_messages > 0) ? $this->messages[$number_of_messages - 1] : '';
	}

	/**
	 * Get all the validation messages.
	 *
	 * @return array
	 */
	public function getMessages(): array
	{
		return $this->messages;
	}

	/**
	 * Check if the object is valid and returns a boolean.
	 *
	 * This method can not be chained.
	 *
	 * @return boolean
	 */
	public function isValid(): bool
	{
		$this->messages = [];
		$this->is_valid = $this->validateObject($this->Object);

		return $this->is_valid;
	}

	/**
	 * Validate the object using validation attributes.
	 *
	 * @param object $Object The object to validate.
	 * @return boolean
	 */
	protected function validateObject(object $Object): bool
	{
		$ReflectionClass      = new ReflectionClass($Object::class);
		$ReflectionProperties = $ReflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
		$object_is_valid      = true;

		foreach ($ReflectionProperties as $Property)
		{
			if (!$this->validateObjectProperty($Object, $Property))
			{
				$object_is_valid = false;
			}
		}

		return $object_is_valid;
	}

	/**
	 * Validate an object's property.
	 *
	 * @param object             $Object   The object with the property to validate.
	 * @param ReflectionProperty $Property The property Reflection object.
	 * @return boolean
	 */
	protected function validateObjectProperty(object $Object, ReflectionProperty $Property): bool
	{
		$property_name  = $Property->name;
		$is_initialized = $Property->isInitialized($Object);
		$initial_value  = ($is_initialized) ? $Object->{$property_name} : $Property->getDefaultValue();
		$is_valid       = true;

		// This metadata is used to hydrate Tailor attributes.
		$metadata = [
			'Property'       => $Property,
			'is_initialized' => $is_initialized,
		];

		// We don't recursively call attributes on attributes from this project to avoid an infinite loop.
		// There shouldn't be any attributes on attribute properties, but just in case.
		if (!($Object instanceof ObjectHelperAttributeInterface))
		{
			$is_valid = $this->processValidatorAttributes($Property, $initial_value, $metadata);
		}

		return $is_valid;
	}

	/**
	 * Process the Validation attributes on a property.
	 *
	 * It processes attributes in order from top to bottom.
	 *
	 * @param ReflectionProperty $Property The property which might have validation attributes.
	 * @param mixed              $value    The value that will be validated by the attributes.
	 * @param array              $metadata Any optional data that might be needed.
	 * @return boolean
	 */
	protected function processValidatorAttributes(ReflectionProperty $Property, mixed $value, array $metadata=[]): bool
	{
		$ReflectionAttributes = $Property->getAttributes(AbstractValidatorAttribute::class, ReflectionAttribute::IS_INSTANCEOF);
		$Hydrator             = new ObjectHydrator();
		$fail_messages_map    = (count($ReflectionAttributes) > 0) ? $this->getCustomValidatorFailureMessages($Property) : [];
		$property_is_valid    = true;

		foreach ($ReflectionAttributes as $ReflectionAttribute)
		{
			$Attribute = $ReflectionAttribute->newInstance();
			$Attribute = $Hydrator->setObject($Attribute)->hydrate($metadata)->getObject();
			$is_valid  = $Attribute->validate($value);

			if (!$is_valid)
			{
				$property_is_valid = false;
				$ValidatorMessage  = $fail_messages_map[$Attribute::class] ?? null;
				$message           = $ValidatorMessage?->message ?? $Attribute->getFailMessage();
				$this->messages[]  = $message;

				if ($ValidatorMessage?->throw_exception ?? false)
				{
					throw new ObjectValidatorFailureException($message);
				}

			}
		}

		return $property_is_valid;
	}

	/**
	 * Get a map of all the validation classes to their ValidatorMessage class.
	 *
	 * @param ReflectionProperty $Property The property which might have validation attributes.
	 * @return array
	 */
	protected function getCustomValidatorFailureMessages(ReflectionProperty $Property): array
	{
		$map = [];

		$ReflectionAttributes = $Property->getAttributes(ValidatorMessage::class, ReflectionAttribute::IS_INSTANCEOF);
		foreach ($ReflectionAttributes as $ReflectionAttribute)
		{
			$Attribute = $ReflectionAttribute->newInstance();

			$map[$Attribute->attribute_class] = $Attribute;
		}

		return $map;
	}
}