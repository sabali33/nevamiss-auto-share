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

		 return <<<TAB

	<a href="?page=nevamiss-settings&tab=$slug" class="nav-tab $active_tab">
        $label
    </a>
TAB;

	}
}