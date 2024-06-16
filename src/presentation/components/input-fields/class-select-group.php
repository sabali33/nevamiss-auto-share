<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Components\Input_Fields;

use Nevamiss\Presentation\Components\Renderable;
use Nevamiss\Presentation\Utils;

class Select_Group_Field extends Select_Field {
	/**
	 * @inheritDoc
	 * @throws \Exception
	 */
	public function render( $attributes = array() ): string {

		$compliment_fields = $attributes['complement_fields'];

		unset( $attributes['complement_fields'] );

		$fields_arr = array_map(
			function ( $field ) {
				return parent::render( $field );
			},
			array( $attributes, ...$compliment_fields )
		);
		$fields     = join( "\n", $fields_arr );

		return <<<DOUBLE_SELECT
            <div class="double-select-fields">
                    $fields
            </div>
    DOUBLE_SELECT;
	}
}
