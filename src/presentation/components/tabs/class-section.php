<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Components\Tabs;

use Nevamiss\Presentation\Components\Renderable;

class Section implements Renderable {

	public function __construct() {
	}

	public function render( $attributes = array() ): string {
		[
			'slug' => $slug,
			'label' => $label,
			'section' => $section,
			'current_section' => $active_section,
		] = $attributes;

		$active_section_class = $active_section === $section ? 'current' : '';
		return "<a href=\"?page=nevamiss-settings&tab=$slug&section=$section\" class=\"$active_section_class\">
	        $label
	    </a>";
	}
}
