<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

trait Render_Interface {
	public function render( $attributes = array() ): string {
		ob_start();

		include NEVAMISS_PATH . self::TEMPLATE_PATH . '.php';

		return ob_get_clean();
	}
}
