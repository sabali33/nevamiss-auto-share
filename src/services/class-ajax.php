<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Presentation\Post_Meta\Post_Meta;

class Ajax {

	public function __construct( private Post_Meta $post_meta ) {
	}

	public function instant_posting_callback(): void {
		if ( ! $this->authorized() ) {
			wp_die( 'unauthorized' );
		}
		$post_id    = filter_input( INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$account_id = filter_input( INPUT_GET, 'account_id', FILTER_SANITIZE_NUMBER_INT );
		try {
			$this->post_meta->share_post_to_account( (int) $post_id, (int) $account_id );
			wp_send_json_success( 'Success', 202 );
		} catch ( \Exception $exception ) {
			wp_send_json_error( $exception->getMessage(), 401 );
		}

		die();
	}

	private function authorized(): bool {
		return isset( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], 'nevamiss-instant-share-action' );
	}
}
