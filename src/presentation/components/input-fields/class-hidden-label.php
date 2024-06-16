<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Components\Input_Fields;

use Nevamiss\Presentation\Components\Renderable;

class Label_Hidden_Input extends Input {

	public function render( $attributes = array() ): string {
		$hidden_field = parent::render( $attributes );

		return <<<LABEL_HIDDEN_INPUT
            <div class="label-hidden-field">
                {$attributes["label"]}
                $hidden_field
                <div class="preview">
                    
                </div>
            </div>
        LABEL_HIDDEN_INPUT;
	}
}
