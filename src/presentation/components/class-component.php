<?php
/**
 * This file is the main component file.
 *
 * @package Sagani\Theme\Components\Component
 */

declare(strict_types=1);

namespace Nevamiss\Presentation\Components;

use Nevamiss\Presentation\Utils;

/**
 * It's a wrapper class for all components.
 */
class Component {

	/**
	 * It holds an instance of a renderable component.
	 *
	 * @var Renderable
	 */
	private Renderable $renderable;

	/**
	 * Attributes of the component.
	 *
	 * @var array
	 */
	private array $attributes;

	/**
	 * Components to be rendered inside this component.
	 *
	 * @var array $inner_components
	 */
	private array $inner_components;

	/**
	 * Sets the relevant properties and makes sure components actually of the required type.
	 *
	 * @param Renderable $renderable A renderable component.
	 * @param array      $attributes Attributes of the component.
	 * @param array      $inner_components components that should be rendered inside the main component.
	 * @throws \Exception An exception is thrown when inner component is not a valid instance.
	 */
	public function __construct( Renderable $renderable, array $attributes, array $inner_components = array() ) {
		$this->attributes           = $attributes;
		$this->renderable           = $renderable;
		$inner_components_are_valid = Utils::every(
			$inner_components,
			function ( $component ) {

				return ( $component instanceof self ) ||
					( is_callable( $component ) &&
						$component() instanceof self ) ||
					is_null( $component );
			}
		);
		if ( ! $inner_components_are_valid ) {
			throw new \Exception( 'Invalid components passed as inner component' );
		}
		$this->inner_components = $inner_components;
	}

	/**
	 * A final render method for all components.
	 *
	 * @return string
	 */
	public function render(): string {
		$attributes = array_merge( $this->attributes, array( 'inner_components' => $this->inner_components ) );
		return $this->renderable->render( $attributes );
	}
}
