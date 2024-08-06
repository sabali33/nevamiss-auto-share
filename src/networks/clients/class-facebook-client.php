<?php

declare(strict_types=1);

namespace Nevamiss\Networks\Clients;

use Exception;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Http_Request;

class Facebook_Client implements Network_Clients_Interface {
	use Has_Credentials_Trait;
	private string $auth_dialog;
	private string $root_url;
	private string $auth_url;
	private string $root_url_versioned;

	use Request_Parameter_Trait;

	/**
	 * @param array{client_id: string, redirect_url: mixed, client_secret: string} $credentials
	 * @throws Exception
	 */
	public function __construct( private Http_Request $request, private array $credentials ) {
		$this->root_url_versioned = 'https://graph.facebook.com/v20.0/';
		$this->auth_dialog        = "{$this->root_url_versioned}dialog/oauth";

		$this->auth_url = "{$this->root_url_versioned}oauth/access_token";
		$this->root_url = 'https://graph.facebook.com/';
	}

	/**
	 * @throws Exception
	 */
	public function auth_link( array $scope = array() ): string {

		$this->has_credentials( $this->credentials['client_id'], $this->credentials['client_secret'] );

		return add_query_arg(
			array(
				'client_id'     => $this->credentials['client_id'],
				'client_secret' => $this->credentials['client_secret'],
				'redirect_uri'  => admin_url( 'admin-post.php?action=facebook' ),
				'auth_type'     => 'rerequest',
				'config_id'     => '2143935749319824',
				'state'         => wp_create_nonce( 'nevamiss-facebook-secret' ),

			),
			$this->auth_dialog
		);
	}

	/**
	 * @throws Exception
	 */
	public function auth( string $code ): array {
		$url = add_query_arg(
			array(
				'client_id'     => $this->credentials['client_id'],
				'client_secret' => $this->credentials['client_secret'],
				'redirect_uri'  => $this->credentials['redirect_url'],
				'code'          => $code,
			),
			$this->auth_url
		);

		$token = $this->request->get( $url );

		$user_data = $this->user_data( $token['access_token'] );

		if (
			! isset( $user_data['data']['app_id'] ) ||
			$user_data['data']['app_id'] !== $this->credentials['client_id'] ||
			! $user_data['data']['is_valid']
		) {
			throw new \Exception( "Facebook App ID,{$this->credentials['client_id']} could not be verified" );
		}
		// Exchange short-live token for long-live token
		['access_token' => $access_token] = $this->long_live_token( $token['access_token'] );

		$user                  = $this->get_user( $user_data['data']['user_id'], $access_token );
		$user['access_token']  = $access_token;
		$user['network_label'] = 'Facebook';

		$user['pages'] = $this->user_pages( $user['id'], $access_token );

		do_action( 'nevamiss_user_network_login', $user, 'facebook' );

		return $user;
	}

	public function get_account( string $access_token ) {
		// TODO: Implement get_account() method.
	}

	public function post( array $data, Network_Account $account ) {
		var_dump( $data );
	}

	/**
	 * @throws Exception
	 */
	private function user_data( $access_token ): array {
		return $this->request->get(
			"{$this->root_url}debug_token?input_token=$access_token&access_token=$access_token"
		);
	}

	/**
	 * @throws Exception
	 */
	private function long_live_token( mixed $access_token ): array {
		$url = add_query_arg(
			array(
				'grant_type'        => 'fb_exchange_token',
				'client_id'         => $this->credentials['client_id'],
				'client_secret'     => $this->credentials['client_secret'],
				'fb_exchange_token' => $access_token,
			),
			$this->auth_url
		);

		return $this->request->get( $url );
	}

	/**
	 * @throws Exception
	 */
	private function get_user( mixed $user_id, $access_token ): array {
		$url = "{$this->root_url_versioned}{$user_id}/";
		return $this->request->get( $url, $this->auth_header( $access_token ) );
	}

	/**
	 * @throws Exception
	 */
	private function user_pages( mixed $id, $access_token ): array {

		$url = "{$this->root_url}{$id}/accounts";

		['data' => $pages] = $this->request->get( $url, $this->auth_header( $access_token ) );

		return array_map(
			function ( $page ) {
				return array(
					'id'           => $page['id'],
					'name'         => $page['name'],
					'access_token' => $page['access_token'],
				);
			},
			$pages
		);
	}
}
