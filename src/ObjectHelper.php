<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilities;

use TodoMakeUsername\DataObjectUtilities\Hydrator\ObjectHydrator;
use TodoMakeUsername\DataObjectUtilities\Shared\ObjectHelperInterface;
use TodoMakeUsername\DataObjectUtilities\Tailor\ObjectTailor;
use TodoMakeUsername\DataObjectUtilities\Validator\ObjectValidator;

class ObjectHelper implements ObjectHelperInterface
{
	protected ?object          $Object;
	protected ?ObjectHydrator  $Hydrator  = null;
	protected ?ObjectTailor    $Tailor    = null;
	protected ?ObjectValidator $Validator = null;

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
	public function setObject(object $Object): ObjectHelper
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
	 * Hydrate the object's public properties.
	 *
	 * @param array $hydrate_data The data to hydrate the object.
	 * @return self
	 */
	public function hydrate(array $hydrate_data): ObjectHelper
	{
		$this->Hydrator ??= new ObjectHydrator();
		$this->Object     = $this->Hydrator->setObject($this->Object)->hydrate($hydrate_data)->getObject();

		return $this;
	}

	/**
	 * Alter (tailor) the data stored in an object's public properties.
	 *
	 * @return self
	 */
	public function tailor(): ObjectHelper
	{
		$this->Tailor ??= new ObjectTailor();
		$this->Object   = $this->Tailor->setObject($this->Object)->tailor()->getObject();

		return $this;
	}

	/**
	 * Validate the object's public properties.
	 *
	 * @return boolean
	 */
	public function isValid(): bool
	{
		$this->Validator ??= new ObjectValidator();
		$is_valid          = $this->Validator->setObject($this->Object)->isValid();

		return $is_valid;
	}

	/**
	 * Get any validation messages.
	 *
	 * @return array
	 */
	public function getValidatorMessages(): array
	{
		return $this->Validator->getMessages();
	}
}