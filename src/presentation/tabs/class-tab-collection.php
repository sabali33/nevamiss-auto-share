<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

class Tab_Collection {
	private array $tabs;
	public function register( string $slug, Tab_Interface $tab ): void {
		$this->tabs[ $slug ] = $tab;
	}

	public function get_all(): array
	{
		return $this->tabs;
	}

	/**
	 * @param string $tab
	 * @return Tab_Interface
	 */
	public function get(string $tab): Tab_Interface
	{
		return $this->tabs[$tab];
	}
}