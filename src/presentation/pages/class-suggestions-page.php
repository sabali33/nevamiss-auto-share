<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Repositories\Posts_Stats_Repository;
use function Nevamiss\sanitize_text_input_field;

class Suggestions_Page extends Page {

	public const TEMPLE_PATH = 'templates/suggestions';
	const SLUG               = 'nevamiss-suggestions';
	public function __construct( Posts_Stats_Repository $stats ) {
		parent::__construct(
			$stats,
			__( 'Suggestions', 'nevamiss' ),
			self::SLUG,
			10,
			Auto_Share_Page::SLUG,
			true
		);
	}

	public function maybe_process_form() {
		if ( empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}
		if ( ! $this->authorize() ) {
			$this->redirect(
				array(
					'message' => __( 'Unauthorized', 'nevamiss' ),
					'status'  => 'error',
				)
			);
			exit;
		}

		$suggestion_data = $this->extract_data( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( empty( $suggestion_data ) ) {
			$this->redirect(
				array(
					'message' => __( 'A suggestion is required', 'nevamiss' ),
					'status'  => 'error',
				)
			);
			exit;
		}

		$this->redirect(
			array(
				'message' => __( 'Thanks for your suggestion', 'nevamiss' ),
				'status'  => 'success',
			)
		);
		exit;
	}

	private function authorize(): bool {
		return (bool) wp_verify_nonce(
			sanitize_text_input_field('_wpnonce', 'post'),
			'nevamiss-suggestion-form-action'
		);
	}

	private function redirect( array $data ): void {
		wp_redirect( add_query_arg( $data, admin_url( 'admin.php?page=nevamiss-suggestions' ) ) );
	}

	private function extract_data( array $post_data ): array {
		if ( ! isset( $post_data['suggestion'] ) || ! trim( $post_data['suggestion'] ) ) {
			return array();
		}
		$data = array();
		foreach ( array( 'fullname', 'email_address', 'suggestion' ) as $key ) {
			$data[ $key ] = isset( $post_data[ $key ] ) ? sanitize_text_field( $post_data[ $key ] ) : null;
		}
		return $data;
	}
}
