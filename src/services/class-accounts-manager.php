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

		$accounts = array();
		foreach ( $posts_data as $posts_datum ) {
			$remote_account = $this->account_repository->get_by_remote_id( $posts_datum['remote_account_id'] );
			if ( $remote_account ) {
				$this->account_repository->update(
					$posts_datum['remote_account_id'],
					array(
						'token'      => $posts_datum['token'],
						'name'       => $posts_datum['name'],
						'expires_in' => $posts_datum['expires_in'] ?? null,
					)
				);
				continue;
			}
			$accounts[] = $this->account_repository->create( $posts_datum );
		}

		if ( in_array( $network, array( 'linkedin', 'x' ) ) ) {
			update_option( "nevamiss-$network-refresh-token", $user['refresh_token'] );
		}

		do_action(
			'nevamiss-user-network-login-complete',
			array(
				'expires_in'  => $user['token_expires_in'],
				'account_ids' => $accounts,
			),
			$network
		);
	}

	/**
	 * @throws \Exception
	 */
	private function prepare_facebook_accounts( array $user ): array {
		$accounts = array(
			array(
				'name'              => $user['name'],
				'network'           => 'facebook',
				'remote_account_id' => $user['id'],
				'token'             => $user['access_token'],
				'expires_in'        => $this->expire_date( $user['token_expires_in'] ),
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

	/**
	 * @throws \Exception
	 */
	private function prepare_linkedin_accounts( array $user ): array {

		$accounts = array(
			array(
				'name'              => $user['name'],
				'network'           => 'linkedin',
				'remote_account_id' => $user['id'],
				'token'             => $user['access_token'],
				'expires_in'        => $this->expire_date( $user['token_expires_in'] ),
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

	/**
	 * @throws \Exception
	 */
	private function prepare_x_accounts( array $user ): array {

		return array(
			array(
				'name'              => $user['name'],
				'network'           => 'x',
				'remote_account_id' => $user['id'],
				'token'             => $user['access_token'],
				'expires_in'        => $user['token_expires_in'] === 'never' ? null : $this->expire_date( ( $user['token_expires_in'] * 24 ) ),
			),
		);
	}

	/**
	 * @throws \Exception
	 */
	public function prepare_instagram_accounts( array $data ): array {
		return array(
			array(
				'name'              => $data['name'],
				'network'           => 'instagram',
				'remote_account_id' => $data['id'],
				'token'             => $data['access_token'],
				'expires_in'        => $this->expire_date( $data['token_expires_in'] ),
			),
		);
	}

	/**
	 * @param int $token_expires_in
	 * @return string
	 * @throws \Exception
	 */
	private function expire_date( int $token_expires_in ): string {
		$date = Date::now();
		$days = round( $token_expires_in / ( 60 * 60 * 24 ) );

		$expire_date = $date->add( \DateInterval::createFromDateString( "$days days" ) );

		return $expire_date->format( 'Y-m-d h:i:s' );
	}
}
