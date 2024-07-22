<?php
/**
 * A component file.
 *
 * @package Nevamiss\Presentation\Components\Wrapper
 */

declare(strict_types=1);

namespace Nevamiss\Presentation\Components;

/**
 * A component class for wrapping other components.
 */
class Wrapper implements Renderable {

	/**
	 * Attributes of the component.
	 *
	 * @var array
	 */
	private array $attributes = array(
		'attributes' => array(),
	);

	/**
	 * A tag for the wrapper.
	 *
	 * @var string
	 */
	protected string $tag = 'div';

	/**
	 * Renders the component.
	 *
	 * @param array{ tag:string, attributes:array, inner_components: array<Component> } $attributes Attributes of the component.
	 * @return string
	 */
	public function render( $attributes = array() ): string {

		$tag             = $attributes['tag'] ?? $this->tag;
		$attributes      = array_merge( $this->attributes, $attributes );
		$html_attributes = $this->parse_attributes( $attributes['attributes'] );

		$text = $attributes['text'] ?? '';

		$inner_components_arr = array_map(
			function ( $component ) {
				return $component->render();
			},
			$attributes['inner_components']
		);
		$inner_components     = join( "\n", $inner_components_arr );

		return "<$tag $html_attributes> $text \n $inner_components </$tag>";
	}

	/**
	 * Parses HTML attributes.
	 *
	 * @param array $attributes Attributes to be parsed.
	 * @return string
	 */
	private function parse_attributes( array $attributes ): string {
		$html_attr = '';
		foreach ( $attributes as $key => $value ) {
			$html_attr .= sprintf( "%s='%s'", $key, $value );
		}
		return $html_attr;
	}
}
