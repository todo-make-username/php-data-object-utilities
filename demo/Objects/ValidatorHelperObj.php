<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpersDemo\Objects;

use TodoMakeUsername\ObjectHelpers\Tailor\Attributes\Trim;
use TodoMakeUsername\ObjectHelpers\Validator\Attributes\NotEmpty;
use TodoMakeUsername\ObjectHelpers\Validator\Attributes\Pattern;

class ValidatorHelperObj implements ObjInterface
{
	#[NotEmpty(fail_message: "The Not Empty field must not be empty.")]
	public $not_empty;

	#[Trim]
	#[NotEmpty(fail_message: "The Trimmed Not Empty field must have a non-whitespace value.")]
	public string $trimmed_not_empty;

	#[Pattern(pattern: '/\d+[A-Za-z]+/', fail_message: "The pattern field must be numbers followed by letters.")]
	public string $pattern;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'not_empty'         => $this->not_empty ?? null,
			'trimmed_not_empty' => $this->trimmed_not_empty ?? null,
			'pattern'           => $this->pattern ?? null,
		];
	}
}