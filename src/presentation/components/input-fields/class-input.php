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

		if ( $attributes['type'] === 'number' ) {
			$input_attributes['min']  = $attributes['min'];
			$input_attributes['max']  = $attributes['max'];
			$input_attributes['step'] = $attributes['step'] ?? 1;
		}

		if ( isset( $attributes['custom_inputs'] ) ) {
			$input_attributes = array_merge( $input_attributes, $attributes['custom_inputs'] );
		}
		$attributes_str = Utils::build_input_attr( $input_attributes );

		$label = $attributes['label'] ?? '';

		return $input_attributes['type'] === 'hidden' ? "<input $attributes_str />" : <<<INPUT
            <label>
                <span>$label</span>
                <input $attributes_str />
            </label>
        INPUT;
	}
}
