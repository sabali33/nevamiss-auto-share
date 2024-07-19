<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Components\Tabs\Tab;

class Stats_Tab implements Tab_Interface {

	public function __construct(private Factory $factory)
	{
	}

	public const SLUG = 'stats';

	public function render($attributes = array()): string
	{
		return "Stats Tab";
	}

	public function label(): ?string
	{
		return __('Statistics', 'nevamiss');
	}

	public function slug(): string
	{
		return self::SLUG;
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function link(string $active_tab): Component
	{
		return $this->factory->component(
			Tab::class,
			[
				'slug' => $this->slug(),
				'label' => $this->label(),
				'active_tab' => $active_tab
			]
		);
	}
}