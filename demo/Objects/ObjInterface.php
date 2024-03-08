<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilitiesDemo\Objects;

interface ObjInterface
{
	/**
	 * Convert class properties and their values into an array
	 *
	 * @return array
	 */
	public function toArray(): array;
}