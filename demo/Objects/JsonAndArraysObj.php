<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpersDemo\Objects;

use TodoMakeUsername\ObjectHelpers\Attributes\Hydrator\JsonDecode;

class JsonAndArraysObj implements ObjInterface
{
	#[JsonDecode(true)]
	public array $val_array;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'array' => $this->val_array,
		];
	}
}