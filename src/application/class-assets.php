<?php

declare(strict_types=1);

namespace Nevamiss\Application;

class Assets {

	public function enqueue_script( string $page ) {
		if ( ! $this->is_page( $page ) ) {
			return;
		}
		['dependencies' => $deps, 'version' => $version ] = require NEVAMISS_PATH . '/build/main.asset.php';
		wp_register_script( 'nevamiss-scripts', NEVAMISS_URL . '/build/main.js', $deps, $version, true );
		wp_register_style( 'nevamiss-flatpickr-style', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css' );
		wp_register_style( 'nevamiss-style', NEVAMISS_URL . '/css/style.css' );
		wp_enqueue_script( 'nevamiss-scripts' );
		wp_enqueue_style( 'nevamiss-flatpickr-style' );
		wp_enqueue_style( 'nevamiss-style' );
	}

	private function is_page( string $page ): bool {
		return in_array(
			$page,
			array(
				'toplevel_page_auto-share-content',
				'auto-share_page_schedules',
				'dashboard_page_edit-schedule',
				'auto-share_page_nevamiss-settings',
				'auto-share_page_nevamiss-stats',
				'post.php',
				'post-new.php',
			)
		);
	}
}
