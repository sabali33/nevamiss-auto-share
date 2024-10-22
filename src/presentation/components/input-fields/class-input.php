<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Components\Input_Fields;

use Nevamiss\Presentation\Components\Renderable;
use Nevamiss\Presentation\Utils;

class Input implements Renderable {

	public function render( $attributes = array() ): string {

		$attributes       = wp_parse_args(
			$attributes,
			array(
				'type'  => 'text',
				'class' => 'input-field',
				'name'  => 'text',
			)
		);
		$input_attributes = array(
			'type'  => $attributes['type'],
			'class' => $attributes['class'],
			'name'  => $attributes['name'],
			'value' => $attributes['value'] ?? '',
		);

		if ( 'number' === $attributes['type'] ) {
			$input_attributes['min']  = $attributes['min'];
			$input_attributes['max']  = $attributes['max'];
			$input_attributes['step'] = $attributes['step'] ?? 1;
		}
		if ( in_array( $attributes['type'], array( 'checkbox', 'radio' ), true ) ) {
			$input_attributes['checked'] = $attributes['checked'] ?? false;
		}
		if ( isset( $attributes['disabled'] ) ) {
			$input_attributes['disabled'] = $attributes['disabled'];
		}
		if ( isset( $attributes['custom_inputs'] ) ) {
			$input_attributes = array_merge( $input_attributes, $attributes['custom_inputs'] );
		}
		$attributes_str = Utils::build_input_attr( $input_attributes );

		$label = $attributes['label'] ?? '';

		return 'hidden' === $input_attributes['type'] ? "<input $attributes_str />" :
			"<p>
	            <label>
	                <span>$label</span>
	                <input $attributes_str />
	            </label>
			</p>";
	}
}
