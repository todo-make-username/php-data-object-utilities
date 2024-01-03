<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Attributes\Converter;

use Attribute;
use TodoMakeUsername\ObjectHelpers\Attributes\Shared\ObjectHelperAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Conversion implements ObjectHelperAttributeInterface
{
	/**
	 * Set the type conversion settings
	 *
	 * @param boolean $strict Use a strict conversion ('123' == 123).
	 */
	public function __construct(public bool $strict=true)
	{}
}