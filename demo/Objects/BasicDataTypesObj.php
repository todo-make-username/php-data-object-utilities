<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpersDemo\Objects;

use TodoMakeUsername\ObjectHelpers\Converter\Attributes\Conversion;

class BasicDataTypesObj implements ObjInterface
{
	public $val_duck;

	public int $val_int;

	#[Conversion(strict: false)]
	public int $val_int_loose;

	public float $val_float;

	public string $val_string;

	public bool $val_bool = false;

	public bool $val_bool_dropdown;

	public array $val_array;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'duck'          => $this->val_duck,
			'int'           => $this->val_int,
			'int loose'     => $this->val_int_loose,
			'float'         => $this->val_float,
			'string'        => $this->val_string,
			'bool'          => $this->val_bool,
			'bool dropdown' => $this->val_bool_dropdown,
			'array'         => $this->val_array,
		];
	}
}