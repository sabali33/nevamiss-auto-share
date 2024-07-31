<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages;

trait Notices_Trait
{
	public function notices(): void {
		$query_args = $this->extract_args($_GET);
		if ( ! $query_args['status'] ) {
			return;
		}
		if ( ! isset( $query_args['message'] ) ) {
			return;
		}
		$decoded_message = rawurldecode($_REQUEST['message']);

		wp_admin_notice(
			stripslashes($decoded_message),
			array(
				'type'               => $_GET['status'],
				'dismissible'        => false,
				'additional_classes' => array( 'inline', 'notice-alt' ),
			)
		);
	}
	public function extract_args(array $post_data): array
	{
		$status = $post_data['status'] ?? null;
		$message = $post_data['message'] ?? null;
		return [
			'status' => $status,
			'message' => $message
		];
	}
}