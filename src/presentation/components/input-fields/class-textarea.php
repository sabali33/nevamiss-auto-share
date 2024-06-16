<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Components\Input_Fields;

use Nevamiss\Presentation\Components\Renderable;
use Nevamiss\Presentation\Utils;

class TextArea implements Renderable {

	public function render( $attributes = array() ): string {
		$attributes = wp_parse_args(
			$attributes,
			array(
				'cols'  => 10,
				'class' => 'text-field',
				'rows'  => '10',
			)
		);

		$input_attributes = array(
			'cols'  => $attributes['cols'],
			'class' => $attributes['class'],
			'rows'  => $attributes['rows'],
			'id'    => $attributes['id'] ?? '',
			'name'  => $attributes['name'] ?? '',
		);

		$attributes_str = Utils::build_input_attr( $input_attributes );

		$label = $attributes['label'] ?? 'Add a label for textarea';
		$value = $attributes['value'] ?? '';
		$id    = $attributes['id'] ?? '';

		return <<<TEXTAREA
            <label for="$id">
                <span>$label</span>
            </label>
            <textarea $attributes_str >$value</textarea>
            
        TEXTAREA;
	}
}
