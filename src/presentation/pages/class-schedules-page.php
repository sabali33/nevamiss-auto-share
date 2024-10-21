<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Presentation\Pages\Tables\Schedules_Table_List;
use function Nevamiss\sanitize_text_input_field;

class Schedules_Page extends Page {

	public const TEMPLE_PATH = 'templates/schedules';
	const SLUG               = 'schedules';

	public function __construct(
		public Schedules_Table_List $table_list,
		private Schedule_Repository $schedule_repository,
	) {
		parent::__construct(
			$table_list,
			'Schedules',
			self::SLUG,
			10,
			Auto_Share_Page::SLUG,
			true
		);
	}

	public function new_link(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		printf(
			'<a href="%1$s" class="page-title-action">%2$s</a>',
			esc_url( admin_url( '?page=edit-schedule' ) ),
			esc_html__( 'Add Schedule', 'nevamiss' )
		);
	}

	public function notices(): void {
		if (
			! isset( $_GET['notice'] ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			! isset( $_GET['type'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		) {
			return;
		}

		$message = sanitize_text_input_field( 'message' );
		if ( ! $message ) {
			return;
		}

		wp_admin_notice(
			$message,
			array(
				'type'               => sanitize_text_input_field( 'type' ),
				'dismissible'        => false,
				'additional_classes' => array( 'inline', 'notice-alt' ),
			)
		);
	}

	/**
	 * @throws \Exception
	 */
	public function bulk_delete(): void {

		if ( ! isset( $_REQUEST['bulk_action'] ) && ! isset( $_REQUEST['bulk_action2'] ) ) {
			$sanitized_request = array_map( 'Nevamiss\sanitize_text_input_field', $_REQUEST ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Inputs are sanitized later
			$this->redirect( $sanitized_request );
			exit;
		}

		if ( $_REQUEST['bulk_action'] !== 'delete_all' || ! isset( $_REQUEST['schedules'] ) ) {

			if ( isset( $_REQUEST['s'] ) ) {
				$this->redirect(
					array(
						's' => sanitize_text_input_field( 's' ),
					)
				);
				exit;
			}
			$this->redirect( array() );
			exit;
		}

		if ( ! wp_verify_nonce( sanitize_text_input_field( '_wpnonce' ), 'bulk-schedules' ) ) {
			$this->redirect(
				array(
					'type'    => 'error',
					'message' => esc_html__( 'Unauthorized', 'nevamiss' ),
				)
			);
			exit;
		}

		if( !isset($_GET['schedules'])){
			$this->redirect(
				array(
					'type'    => 'error',
					'message' => esc_html__( 'No schedules found', 'nevamiss' ),
				)
			);
			exit;
		}
		$schedules = map_deep(
			wp_unslash($_GET['schedules']),
			function(mixed $schedule_id){
				return (int) sanitize_text_field($schedule_id);
			}
		);

		foreach ( $schedules as $schedule ) {
			$this->schedule_repository->delete( $schedule );
		}

		$this->redirect(
			array(
				'type'    => 'success',
				'message' => __( 'Schedule Successfully Deleted', 'nevamiss' ),
			)
		);
		exit;
	}

	private function redirect( array $data ): void {
		$url = add_query_arg( $data, admin_url( 'admin.php?page=schedules' ) );
		wp_redirect( $url );
	}
}
