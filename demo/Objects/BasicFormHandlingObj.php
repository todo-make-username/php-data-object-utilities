<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilitiesDemo\Objects;

use TodoMakeUsername\DataObjectUtilities\Attributes\Converter\ConversionSettings;

class BasicFormHandlingObj implements ObjInterface
{
	public $val_duck;

	public int $val_int;

	#[ConversionSettings(strict: false)]
	public int $val_int_loose;

	public float $val_float;

	public string $val_string;

	public bool $val_bool = false;

	public bool $val_bool_dropdown;

	#[ConversionSettings(strict: false)]
	public bool $val_bool_truthy;

	public array  $multiselect = [];

	public array  $multi_checkbox = [];

	public string $radio;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'duck'           => $this->val_duck,
			'int'            => $this->val_int,
			'int loose'      => $this->val_int_loose,
			'float'          => $this->val_float,
			'string'         => $this->val_string,
			'bool'           => $this->val_bool,
			'bool dropdown'  => $this->val_bool_dropdown,
			'bool truthy'    => $this->val_bool_truthy,
			'multiselect'    => $this->multiselect,
			'multi_checkbox' => $this->multi_checkbox,
			'radio'          => $this->radio,
		];
	}
}