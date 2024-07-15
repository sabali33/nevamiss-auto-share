<?php

declare(strict_types=1);

namespace Nevamiss\Networks;

use Nevamiss\Networks\Clients\Facebook_Client;
use Nevamiss\Networks\Clients\Linkedin_Client;
use Nevamiss\Networks\Clients\X_Client;
use Nevamiss\Services\Accounts_Manager;

class Network_Authenticator {
	public function __construct(
		private Media_Network_Collection $collection,
		private Accounts_Manager $accounts_manager
	) {
	}

	public function facebook_auth(): void {
		if ( ! $this->authorize( 'facebook' ) ) {
			$this->redirect( $this->unauthorize_message() );
			exit;
		}
		$code = $_GET['code'];
		/**
		 * @var Facebook_Client $facebook_client
		 */
		$facebook_client = $this->collection->get( 'facebook' );
		try {
			$data = $facebook_client->auth( $code );

			$this->accounts_manager->network_login_callback( $data, 'facebook' );

			$this->redirect(
				array(
					'status'  => 'success',
					'message' => $this->success_message( $data ),
				)
			);
		} catch ( \Exception $exception ) {
			$this->redirect( $this->error_message( $exception ) );
		}
	}

	/**
	 * @throws \Exception
	 */
	public function linkedin_auth(): void {
		if ( ! $this->authorize( 'linkedin' ) ) {
			$this->redirect( $this->unauthorize_message() );
			exit;
		}

		$code = $_GET['code'];
		/**
		 * @var Linkedin_Client $linkedin_client
		 */
		$linkedin_client = $this->collection->get( 'linkedin' );

		try {
			$data = $linkedin_client->auth( $code );

			$this->accounts_manager->network_login_callback( $data, 'linkedin' );

			$this->redirect(
				array(
					'status'  => 'success',
					'message' => $this->success_message( $data ),
				)
			);

		} catch ( \Exception $exception ) {
			$this->redirect( $this->error_message( $exception ) );
			exit;
		}
	}
	private function authorize( string $network ): bool {
		return isset( $_GET['state'] ) && wp_verify_nonce( $_GET['state'], "nevamiss-$network-secret" );
	}

	private function redirect( array $data ): void {
		$redirect_url = add_query_arg(
			$data,
			admin_url( 'admin.php?page=nevamiss-settings' )
		);

		wp_redirect( $redirect_url );
	}

	/**
	 * @param array $data
	 * @return string
	 */
	private function success_message( array $data ): string {
		return urlencode( "{$data['name']} has successfully logged in to {$data['network_label']}!" );
	}

	/**
	 * @return array
	 */
	private function unauthorize_message(): array {
		return array(
			'status'  => 'error',
			'message' => __( 'Not authorized', 'nevamiss' ),
		);
	}

	/**
	 * @param \Exception $exception
	 * @return array
	 */
	private function error_message( \Exception $exception ): array {
		return array(
			'status'  => 'error',
			'message' => $exception->getMessage(),
		);
	}

	public function x_auth(): void {
		if ( ! $this->authorize( 'x' ) ) {
			$this->redirect( $this->unauthorize_message() );
			exit;
		}
		if ( isset( $_GET['error'] ) ) {
			$this->redirect( $this->error_message( $_GET['error'] ) );
			exit;
		}
		$code = $_GET['code'];
		/**
		 * @var X_Client $x_client
		 */
		$x_client = $this->collection->get( 'x' );

		$_SESSION['code'] = $code;

		try {
			$data = $x_client->auth( $code );

			$this->accounts_manager->network_login_callback( $data, 'x' );

			$this->redirect(
				array(
					'status'  => 'success',
					'message' => $this->success_message( $data ),
				)
			);

		} catch ( \Exception $exception ) {
			$this->redirect( $this->error_message( $exception ) );
			exit;
		}
	}
}
