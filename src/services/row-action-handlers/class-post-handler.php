<?php

namespace Nevamiss\Services;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Repositories\Schedule_Repository;

class Post_Handler {

	private ?string $schedules_home;

	public function __construct(
		private Schedule_Repository $schedule_repository,
		private WP_Cron_Service $cron_service,
		private Schedule_Post_Manager $post_manager
	) {
		$this->schedules_home = admin_url( 'admin.php?page=schedules' );
	}

	/**
	 * @throws Not_Found_Exception
	 * @throws \Exception
	 */
	public function share_schedule_posts_callback(): void {
		$authorized = $this->authorize();

		if ( ! $authorized ) {
			wp_die( 'Unauthorized' );
		}

		$schedule_id = (int) sanitize_text_field( $_GET['schedule_id'] );

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
		$authorized = $this->authorize();

		if ( ! $authorized ) {
			wp_die( 'Unauthorized' );
		}
		$schedule_id = (int) sanitize_text_field( $_GET['schedule_id'] );

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
		$schedule_id = sanitize_text_field( $_GET['schedule_id'] );

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

	/**
	 * @return void
	 */
	private function authorize(): bool {
		return isset( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], 'nevamiss_schedules' );
	}

	private function redirect( array $args ): void {
		$url = add_query_arg( $args, $this->schedules_home );
		wp_redirect( $url );
	}
}
