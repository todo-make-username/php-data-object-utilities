<?php declare(strict_types=1);

namespace TodoMakeUsername\ObjectHelpersDemo\Objects;

use TodoMakeUsername\ObjectHelpers\Tailor\Attributes\StrReplace;
use TodoMakeUsername\ObjectHelpers\Tailor\Attributes\Trim;
use TodoMakeUsername\ObjectHelpers\Tailor\Attributes\UseDefaultOnEmpty;

class TailorHelperObj implements ObjInterface
{
	#[Trim]
	public string $trim;

	#[UseDefaultOnEmpty]
	public string $default_on_empty = 'test';

	#[StrReplace('World', 'You')]
	public string $str_replace;

	#[StrReplace([ 'H', 'W' ], [ 'Hello', 'World' ])]
	#[Trim()]
	public string $mixed;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'trim'             => $this->trim,
			'default_on_empty' => $this->default_on_empty,
			'str_replace'      => $this->str_replace,
			'mixed'            => $this->mixed,
		];
	}
}