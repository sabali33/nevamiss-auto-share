<?php

declare(strict_types=1);

namespace Nevamiss\Services\Row_Action_Handlers;

trait Row_Action_Trail {

	/**
	 * @return bool
	 */
	private function authorize(): bool {
		return isset( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], $this->nonce_action );
	}

	private function redirect( array $args ): void {
		$url = add_query_arg( $args, $this->page_home );
		wp_redirect( $url );
	}
}
