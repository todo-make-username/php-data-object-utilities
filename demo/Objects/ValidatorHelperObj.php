<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpersDemo\Objects;

use TodoMakeUsername\ObjectHelpers\Attributes\Tailor\Trim;
use TodoMakeUsername\ObjectHelpers\Attributes\Validator\NotEmpty;
use TodoMakeUsername\ObjectHelpers\Attributes\Validator\RegexMatch;
use TodoMakeUsername\ObjectHelpers\Attributes\Validator\ValidatorMessage;

class ValidatorHelperObj implements ObjInterface
{
	#[NotEmpty]
	public $not_empty;

	#[Trim]
	#[NotEmpty]
	public string $trimmed_not_empty;

	#[RegexMatch(pattern: '/^\d+[A-Za-z]+$/')]
	public string $pattern;

	#[NotEmpty]
	#[ValidatorMessage(NotEmpty::class, 'This is my custom error message!')]
	public $custom_message;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'not_empty'         => $this->not_empty ?? null,
			'trimmed_not_empty' => $this->trimmed_not_empty ?? null,
			'pattern'           => $this->pattern ?? null,
			'custom_message'    => $this->custom_message ?? null,
		];
	}
}