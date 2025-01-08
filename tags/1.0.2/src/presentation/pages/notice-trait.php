<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages;

use function Nevamiss\sanitize_text_input_field;

trait Notices_Trait {

	public function notices(): void {
		$query_args = array(
			'status'  => sanitize_text_input_field( 'status' ),
			'message' => sanitize_text_input_field( 'message' ),
		);
		if ( ! $query_args['status'] ) {
			return;
		}
		if ( ! isset( $query_args['message'] ) ) {
			return;
		}

		$decoded_message = wp_kses_post( stripslashes( $query_args['message'] ) );

		wp_admin_notice(
			$decoded_message,
			array(
				'type'               => sanitize_text_input_field( 'status' ),
				'dismissible'        => false,
				'additional_classes' => array( 'inline', 'notice-alt' ),
			)
		);
	}
}
