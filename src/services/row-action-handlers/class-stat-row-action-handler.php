<?php

declare(strict_types=1);

namespace Nevamiss\Services\Row_Action_Handlers;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Repositories\Posts_Stats_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Services\Schedule_Post_Manager;
use Nevamiss\Services\WP_Cron_Service;

class Stats_Row_Action_Handler {


	use Row_Action_Trail;

	private string $nonce_action;
	private ?string $page_home;

	public function __construct( private Posts_Stats_Repository $stats_repository ) {
		$this->page_home    = admin_url( 'admin.php?page=nevamiss-settings&tab=stats' );
		$this->nonce_action = 'nevamiss_stats';
	}

	public function delete_stat_row_callback(): void {
		if ( ! $this->authorize() ) {
			wp_die( 'Unauthorized' );
		}

		$stat_id = (int) sanitize_text_field( $_GET['entry_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		try {
			$this->stats_repository->delete( $stat_id );
			$this->redirect(
				array(
					'status'  => 'success',
					'message' => __( 'Stats data row deleted', 'nevamiss' ),
					'notice'  => true,
				)
			);
		} catch ( \Exception $exception ) {
			$this->redirect(
				array(
					'status'  => 'error',
					'message' => $exception->getMessage(),
					'notice'  => true,
				)
			);
		}
	}
}
