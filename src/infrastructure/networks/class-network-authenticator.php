<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Networks;

use Nevamiss\Infrastructure\Networks\Clients\Facebook_Client;
use Nevamiss\Infrastructure\Networks\Clients\Instagram_Client;
use Nevamiss\Infrastructure\Networks\Clients\Linkedin_Client;
use Nevamiss\Infrastructure\Networks\Clients\X_Client;
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
		$code = $_GET['code']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
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

		if ( isset( $_GET['error'] ) && isset( $_GET['error_reason'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->redirect(
				array(
					'status'  => 'error',
					'message' => new \Exception( $_GET['error_description'] ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				)
			);
			exit;
		}

		$code = $_GET['code']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

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

	private function authorize( string $network, $nonce_key = 'state' ): bool {
		return isset( $_GET[ $nonce_key ] ) && wp_verify_nonce( $_GET[ $nonce_key ], "nevamiss-$network-secret" );
	}

	private function redirect( array $data ): void {
		$redirect_url = add_query_arg(
			$data,
			admin_url( 'admin.php?page=nevamiss-settings&tab=network-accounts' )
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
		/**
		 * @var X_Client $x_client
		 */
		$x_client = $this->collection->get( 'x' );

		if ( ! $x_client->is_version( 'v1' ) ) {
			if ( ! $this->authorize( 'x' ) ) {
				$this->redirect( $this->unauthorize_message() );
				exit;
			}
		}

		if ( isset( $_GET['error'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->redirect( $this->error_message( new \Exception( $_GET['error_description'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			exit;
		}

		$code = $x_client->code();

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

	public function instagram_auth(): void {
		if ( ! $this->authorize( 'instagram' ) ) {
			$this->redirect( $this->unauthorize_message() );
			exit;
		}
		if ( isset( $_GET['error'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->redirect( $this->error_message( new \Exception( $_GET['error_description'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			exit;
		}
		$code = $_GET['code']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/**
		 * @var Instagram_Client $instagram_client
		 */
		$instagram_client = $this->collection->get( 'instagram' );

		try {
			$data = $instagram_client->auth( $code );

			$this->accounts_manager->network_login_callback( $data, 'instagram' );

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