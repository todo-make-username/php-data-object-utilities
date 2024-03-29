<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilitiesDemo\Objects;

use TodoMakeUsername\DataObjectUtilities\Attributes\Hydrator\Required;

class RequiredObj implements ObjInterface
{
	#[Required]
	public string $required;

	#[Required]
	//#[NotEmpty] @phpstan-ignore-line
	public string $required_not_empty;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'required'           => $this->required,
			'required_not_empty' => $this->required_not_empty,
		];
	}
}