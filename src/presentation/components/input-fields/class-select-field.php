<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Components\Input_Fields;

use Nevamiss\Presentation\Components\Renderable;
use Nevamiss\Presentation\Utils;

class Select_Field implements Renderable {
	private array $attributes = array(
		'choices' => array(),
		'label'   => 'Enter select label',
		'nevamiss',
		'id'      => 'select-id',
	);
	/**
	 * @inheritDoc
	 * @throws \Exception
	 */
	public function render( $attributes = array() ): string {
		$attributes = array_merge( $this->attributes, $attributes );

		$input_attributes = array(
			'class'    => $attributes['class'] ?? '',
			'name'     => $attributes['name'] ?? 'select_field',
			'id'       => $attributes['id'] ?? 'select-field',
			'multiple' => isset( $attributes['multiple'] ) && $attributes['multiple'],
		);

		if ( isset( $attributes['custom_inputs'] ) ) {
			$input_attributes = array_merge( $input_attributes, $attributes['custom_inputs'] );
		}

		$input_attr = Utils::build_input_attr( $input_attributes );

		if ( isset( $attributes['multiple'] ) && $attributes['multiple'] ) {
			$attributes['value'] = $attributes['value'] ?? array();
		}
		ob_start();

		include NEVAMISS_PATH . 'resources/templates/select-field.php';

		return ob_get_clean();
	}
}
