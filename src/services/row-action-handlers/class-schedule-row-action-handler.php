<?php

declare(strict_types=1);

namespace Nevamiss\Services\Row_Action_Handlers;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Services\Schedule_Post_Manager;
use Nevamiss\Services\WP_Cron_Service;

class Schedule_Row_Action_Handler {

	use Row_Action_Trail;

	private ?string $page_home;
	private string $nonce_action;

	public function __construct(
		private Schedule_Repository $schedule_repository,
		private WP_Cron_Service $cron_service,
		private Schedule_Post_Manager $post_manager
	) {
		$this->page_home    = admin_url( 'admin.php?page=schedules' );
		$this->nonce_action = 'nevamiss_schedules';
	}

	/**
	 * @throws \Exception
	 */
	public function share_schedule_posts_callback(): void {

		if ( ! $this->authorize() ) {
			wp_die( 'Unauthorized' );
		}

		$schedule_id = (int) sanitize_text_field( $_GET['schedule_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		try {
			$this->post_manager->run( $schedule_id );
			$this->redirect(
				array(
					'type'    => 'success',
					'message' => __( 'Schedule post shared', 'nevamiss' ),
					'notice'  => true,
				)
			);

			exit;
		} catch ( Not_Found_Exception | \Exception $exception ) {
			$this->redirect(
				array(
					'type'    => 'error',
					'message' => $exception->getMessage(),
					'notice'  => true,
				)
			);

			exit;
		}
	}

	/**
	 * @throws \Exception
	 */
	public function delete_schedule_callback(): void {

		if ( ! $this->authorize() ) {
			wp_die( 'Unauthorized' );
		}
		$schedule_id = (int) sanitize_text_field( $_GET['schedule_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		try {
			$this->cron_service->unschedule( $schedule_id );
			$this->schedule_repository->delete( $schedule_id );

			$this->redirect(
				array(
					'type'    => 'success',
					'message' => __( 'Schedule deleted', 'nevamiss' ),
					'notice'  => true,
				)
			);
			exit;
		} catch ( \Exception $exception ) {
			$this->redirect(
				array(
					'type'    => 'error',
					'message' => $exception->getMessage(),
					'notice'  => true,
				)
			);
			exit;
		}
	}

	public function unschedule_callback(): void {
		$authorized = $this->authorize();

		if ( ! $authorized ) {
			wp_die( 'Unauthorized' );
		}
		$schedule_id = sanitize_text_field( $_GET['schedule_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$this->cron_service->unschedule( intval( $schedule_id ) );

		$this->redirect(
			array(
				'type'    => 'success',
				'message' => __( 'Unschedule cron', 'nevamiss' ),
				'notice'  => true,
			)
		);
		exit;
	}
}
