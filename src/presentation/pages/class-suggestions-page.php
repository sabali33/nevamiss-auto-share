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

	public function maybe_process_form(): void {

		if ( ! $this->authorize() ) {
			$this->redirect(
				array(
					'message' => __( 'Unauthorized', 'nevamiss' ),
					'status'  => 'error',
				)
			);
			exit;
		}
		if ( ! isset( $_POST['suggestion'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$this->redirect(
				$this->suggestion_required_message()
			);
			exit;
		}
		$data = array();
		foreach ( array( 'fullname', 'email_address', 'suggestion' ) as $key ) {
			$data[ $key ] = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash($_POST[ $key ]) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		if ( !$data['suggestion'] ) {
			$this->redirect(
				$this->suggestion_required_message()
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
			sanitize_text_input_field( '_wpnonce', 'post' ),
			'nevamiss-suggestion-form-action'
		);
	}

	private function redirect( array $data ): void {
		wp_redirect( add_query_arg( $data, admin_url( 'admin.php?page=nevamiss-suggestions' ) ) );
	}

	/**
	 * @return array
	 */
	private function suggestion_required_message(): array
	{
		return array(
			'message' => __('A suggestion is required', 'nevamiss'),
			'status' => 'error',
		);
	}
}
