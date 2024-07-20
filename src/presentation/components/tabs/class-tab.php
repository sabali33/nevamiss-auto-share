<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Components\Tabs;

use Nevamiss\Presentation\Components\Renderable;

class Tab implements Renderable
{
	public function __construct()
	{
	}

	public function render($attributes = array()): string
	{
		 [
			 'slug' => $slug,
			 'label' => $label,
			 'active_tab' => $active_tab
		 ] = $attributes;
		$active_tab_class = $active_tab === $slug ? 'nav-tab-active' : '';
		 return <<<TAB

	<a href="?page=nevamiss-settings&tab=$slug" class="nav-tab $active_tab_class">
        $label
    </a>
TAB;

	}
}