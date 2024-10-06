<?php

declare(strict_types=1);

namespace Nevamiss\Application;

class Assets {

	public function enqueue_script( string $page ): void {

		if ( ! $this->is_page( $page ) ) {
			return;
		}

		['dependencies' => $deps, 'version' => $version ] = require NEVAMISS_PATH . '/build/main.asset.php';
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_register_script( 'nevamiss-scripts', NEVAMISS_URL . 'build/main.js', array( ...$deps, 'jquery-ui-sortable' ), $version, true );
		wp_register_style( 'nevamiss-flatpickr-style', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), 20240608 );
		wp_register_style( 'nevamiss-style', NEVAMISS_URL . 'css/style.css', array(), fileatime( NEVAMISS_PATH . '/css/style.css' ) );
		wp_enqueue_script( 'nevamiss-scripts' );
		wp_enqueue_style( 'nevamiss-flatpickr-style' );
		wp_enqueue_style( 'nevamiss-style' );

		wp_localize_script(
			'nevamiss-scripts',
			'nevamiss',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'nevamiss_general_nonce' ),
				'messages' => array(
					'sort_pending_text' => esc_html__( 'Sorting...', 'nevamiss' ),
					'sort_success_text' => esc_html__( 'Sorted', 'nevamiss' ),
					'sort_failure_text' => esc_html__( 'Failed to sort', 'nevamiss' ),
				),

			)
		);
	}

	private function is_page( string $page ): bool {
		return in_array(
			$page,
			array(
				'toplevel_page_nevamiss-auto-share-content',
				'auto-share_page_schedules',
				'dashboard_page_edit-schedule',
				'admin_page_edit-schedule',
				'auto-share_page_nevamiss-settings',
				'auto-share_page_nevamiss-suggestions',
				'post.php',
				'post-new.php',
			)
		);
	}
}
