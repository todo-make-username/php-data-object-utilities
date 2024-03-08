<?php declare(strict_types=1);

namespace TodoMakeUsername\DataObjectUtilitiesDemo\Objects;

use TodoMakeUsername\DataObjectUtilities\Attributes\Tailor\StrReplace;
use TodoMakeUsername\DataObjectUtilities\Attributes\Tailor\Trim;
use TodoMakeUsername\DataObjectUtilities\Attributes\Tailor\UseDefaultOnEmpty;

class TailorHelperObj implements ObjInterface
{
	#[Trim]
	public string $trim;

	#[UseDefaultOnEmpty]
	public string $default_on_empty = 'test';

	#[StrReplace('World', 'You')]
	public string $str_replace;

	#[Trim]
	#[UseDefaultOnEmpty]
	public string $useful_mix = 'I was empty';

	#[StrReplace([ 'H', 'W' ], [ 'Hello', 'World' ])]
	#[Trim()]
	public string $custom_mix;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'trim'             => $this->trim,
			'default_on_empty' => $this->default_on_empty,
			'str_replace'      => $this->str_replace,
			'useful_mix'       => $this->useful_mix,
			'custom_mix'       => $this->custom_mix,
		];
	}
}