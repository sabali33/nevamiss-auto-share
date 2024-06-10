<?php
/**
 * A file that contains interface for rendering components.
 *
 * @package Sagani\Theme\Components\Renderable
 */

declare(strict_types=1);

namespace Nevamiss\Presentation\Components;

/**
 * An interface for a class that needs to render HTML.
 */
interface Renderable {
	/**
	 * Defines a render method for which implement classes much declare.
	 *
	 * @param array $attributes Attributes to be passed to an implementing component.
	 * @return string
	 */
	public function render( $attributes = array() ): string;
}
