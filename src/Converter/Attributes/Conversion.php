<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpers\Converter\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Conversion
{
	/**
	 * Set the type conversion settings
	 *
	 * @param boolean $strict Use a strict conversion ('123' == 123).
	 */
	public function __construct(public bool $strict=true)
	{}
}