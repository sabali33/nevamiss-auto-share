<?php

declare(strict_types=1);

namespace Nevamiss\Services\Row_Action_Handlers;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Services\Schedule_Post_Manager;
use Nevamiss\Services\WP_Cron_Service;

class Accounts_Row_Action_Handler {
	use Row_Action_Trail;

	private ?string $page_home;
	private string $nonce_action;

	public function __construct( private Network_Account_Repository $account_repository ) {
		$this->page_home    = admin_url( 'admin.php?page=nevamiss-settings&tab=network-accounts' );
		$this->nonce_action = 'nevamiss_network_accounts';
	}

	public function logout_accounts_callback(): void {
		$authorized = $this->authorize();

		if ( ! $authorized ) {
			wp_die( 'Unauthorized' );
		}
		$account_id = (int) sanitize_text_field( $_GET['account_id'] );

		try {
			$this->account_repository->delete( $account_id );

			$this->redirect(
				array(
					'type'    => 'success',
					'message' => __( 'Accounts Logged out', 'nevamiss' ),
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
}
