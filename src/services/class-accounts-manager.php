<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\Repositories\Network_Account_Repository;

class Accounts_Manager {

	public function __construct( private Network_Account_Repository $account_repository ) {
	}

	/**
	 * @throws \Exception
	 */
	public function network_login_callback( array $user, string $network ): void {
		$posts_data = match ( $network ) {
			'facebook' => $this->prepare_facebook_accounts( $user ),
			'linkedin' => $this->prepare_linkedin_accounts( $user ),
			'x' => $this->prepare_x_accounts( $user ),
			'instagram' => $this->prepare_instagram_accounts( $user ),
			default => apply_filters( 'nevamiss-user-auth-data', $user, $network )
		};

		$accounts = [];
		foreach ( $posts_data as $posts_datum ) {
			$remote_account = $this->account_repository->get_by_remote_id( $posts_datum['remote_account_id'] );
			if ( $remote_account ) {
				$this->account_repository->update(
					$posts_datum['remote_account_id'],
					array(
						'token' => $posts_datum['token'],
						'name'  => $posts_datum['name'],
					)
				);
				continue;
			}
			$accounts[] = $this->account_repository->create( $posts_datum );
		}

		if('linkedin' === $network){
			update_option('nevamiss-linkedin-refresh-token', $user['refresh_token']);
		}

		do_action(
			'nevamiss-user-network-login-complete',
			array(
				'expires_in' => $user['expires_in'],
				'account_ids' => $accounts
			),
			$network
		);
	}

	private function prepare_facebook_accounts( array $user ): array {
		$accounts = array(
			array(
				'name'              => $user['name'],
				'network'           => 'facebook',
				'remote_account_id' => $user['id'],
				'token'             => $user['access_token'],
			),
		);
		foreach ( $user['pages'] as $page ) {
			$accounts[] = array(
				'name'              => $page['name'],
				'network'           => 'facebook',
				'remote_account_id' => $page['id'],
				'token'             => $page['access_token'],
				'parent_remote_id'  => $user['id'],
			);
		}
		return $accounts;
	}

	private function prepare_linkedin_accounts( array $user ): array {

		$accounts = array(
			array(
				'name'              => $user['name'],
				'network'           => 'linkedin',
				'remote_account_id' => $user['id'],
				'token'             => $user['access_token'],
			),
		);
		foreach ( $user['organizations'] as $organization ) {

			if ( ! isset( $organization['id'] ) || ! isset( $organization['name'] ) ) {
				continue;
			}

			$accounts[] = array(
				'name'              => $organization['name'],
				'network'           => 'linkedin',
				'remote_account_id' => $organization['id'],
				'token'             => '',
				'parent_remote_id'  => $user['id'],
			);
		}
		return $accounts;
	}

	private function prepare_x_accounts( array $user ): array {
		return array(
			array(
				'name'              => $user['name'],
				'network'           => 'x',
				'remote_account_id' => $user['id'],
				'token'             => $user['access_token'],
			),
		);
	}

	public function prepare_instagram_accounts( array $data ): array {
		return array(
			array(
				'name'              => $data['name'],
				'network'           => 'instagram',
				'remote_account_id' => $data['id'],
				'token'             => $data['access_token'],
			),
		);
	}
}
