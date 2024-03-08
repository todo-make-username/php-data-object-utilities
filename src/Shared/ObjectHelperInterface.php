<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilities\Shared;

interface ObjectHelperInterface
{
	/**
	 * Set the object that the helper will use.
	 *
	 * @param object $Object The object that the helper will use.
	 * @return self
	 */
	public function setObject(object $Object): ObjectHelperInterface;

	/**
	 * Get the modified object or null if something failed.
	 *
	 * @return ?object
	 */
	public function getObject(): ?object;
}