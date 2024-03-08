<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilities\Attributes\Hydrator;

use Attribute;
use TodoMakeUsername\DataObjectUtilities\Attributes\Shared\ObjectHelperAttributeInterface;

/**
 * This sets the various settings for hydration.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class HydratorSettings implements ObjectHelperAttributeInterface
{
	/**
	 * Set Any Hydration Settings
	 *
	 * @param boolean $hydrate Hydrate this or not.
	 * @param boolean $convert Convert this or not.
	 */
	public function __construct(public readonly bool $hydrate=true, public readonly bool $convert=true)
	{}
}