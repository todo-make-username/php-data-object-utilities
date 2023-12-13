<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpersDemo\Objects;

use TodoMakeUsername\ObjectHelpers\Hydrator\Attributes\Required;

class RequiredObj implements ObjInterface
{
	#[Required()]
	public string $required;

	#[Required(not_empty: true)]
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