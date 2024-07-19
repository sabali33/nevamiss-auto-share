<?php
/**
 * A component file.
 *
 * @package Nevamiss\Presentation\Components\Component_Runner
 */

declare(strict_types=1);

namespace Nevamiss\Presentation\Components;

/**
 * A component class that accepts a list of components and renders them without a wrapper.
 */
class Component_Runner implements Renderable {

	/**
	 * A render function of the component.
	 *
	 * @param array{inner_components: array<Component>} $attributes Attributes of the components.
	 * @return string
	 */
	public function render( $attributes = array() ): string {

		['inner_components' => $inner_components ] = $attributes;

		return array_reduce(
			$inner_components,
			function( string $acc, Component $component ) {
				$acc .= $component->render() . PHP_EOL;
				return $acc;
			},
			''
		);
	}
}
