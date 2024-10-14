<?php

declare(strict_types=1);

namespace Nevamiss\Services\Row_Action_Handlers;

use function Nevamiss\sanitize_text_input_field;

trait Row_Action_Trail {

	/**
	 * @return bool
	 */
	private function authorize(): bool {
		return isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_input_field( 'nonce' ), $this->nonce_action );
	}

	private function redirect( array $args ): void {
		$url = add_query_arg( $args, $this->page_home );
		wp_redirect( $url );
	}
}
