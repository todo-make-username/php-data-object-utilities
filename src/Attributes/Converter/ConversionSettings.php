<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Attributes\Converter;

use Attribute;
use TodoMakeUsername\ObjectHelpers\Attributes\Shared\ObjectHelperAttributeInterface;

/**
 * This sets the various settings for type conversions.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ConversionSettings implements ObjectHelperAttributeInterface
{
	/**
	 * Set the type conversion settings
	 *
	 * @param boolean $strict Use a strict conversion ('123' == 123).
	 */
	public function __construct(public readonly bool $strict=true)
	{}
}