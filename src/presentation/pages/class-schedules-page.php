<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Presentation\Pages\Tables\Schedules_Table_List;

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
			esc_html__( 'Add Schedule' )
		);
	}

	public function notices(): void {
		if (
			! isset( $_GET['notice'] ) &&
			! isset( $_GET['type'] )
		) {
			return;
		}
		if ( ! ( isset( $_GET['message'] ) && $_GET['message'] ) ) {
			return;
		}
		wp_admin_notice(
			$_GET['message'],
			array(
				'type'               => $_GET['type'],
				'dismissible'        => false,
				'additional_classes' => array( 'inline', 'notice-alt' ),
			)
		);
	}

	/**
	 * @throws \Exception
	 */
	public function bulk_delete(): void {
		if ( ! isset( $_REQUEST['action'] ) && ! isset( $_REQUEST['action2'] ) ) {
			return;
		}

		if ( $_REQUEST['action'] !== 'delete_all' || ! isset( $_REQUEST['schedules'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-schedules' ) ) {
			return;
		}

		['schedules' => $schedules] = filter_input_array(
			INPUT_GET,
			array(
				'schedules' => array(
					'filter' => FILTER_VALIDATE_INT,
					'flags'  => FILTER_REQUIRE_ARRAY,
				),
			)
		);

		foreach ( $schedules as $schedule ) {
			$this->schedule_repository->delete( $schedule );
		}
	}
}
