<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilities\Attributes\Tailor;

use Attribute;
use TodoMakeUsername\DataObjectUtilities\Tailor\ObjectTailoringException;

/**
 * Sets the value to the default value of the property if it passes an empty() check.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class UseDefaultOnEmpty extends AbstractTailorAttribute
{
	/**
	 * {@inheritDoc}
	 */
	public function process(mixed $value): mixed
	{
		$property_name = $this->Property->name;
		$has_default   = $this->Property->hasDefaultValue();

		if (!$has_default)
		{
			throw new ObjectTailoringException('The property: "'.$property_name.'" must have a default value for the DefaultOnEmpty attribute.');
		}

		if (empty($value))
		{
			$value = $this->Property->getDefaultValue();
		}

		return $value;
	}
}