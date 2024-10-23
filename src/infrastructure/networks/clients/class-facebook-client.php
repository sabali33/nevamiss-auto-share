<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Networks\Clients;

use Exception;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Infrastructure\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Http_Request;

class Facebook_Client implements Network_Clients_Interface {
	use Has_Credentials_Trait;
	use Request_Parameter_Trait;

	private string $auth_dialog;
	private string $root_url;
	private string $auth_url;
	private string $root_url_versioned;


	/**
	 * @param array{client_id: string, redirect_url: mixed, client_secret: string} $credentials
	 * @throws Exception
	 */
	public function __construct( private Http_Request $request, private array $credentials ) {
		$this->root_url_versioned = 'https://graph.facebook.com/v20.0/';
		$this->auth_dialog        = 'https://www.facebook.com/v20.0/dialog/oauth';

		$this->auth_url = "{$this->root_url_versioned}oauth/access_token";
		$this->root_url = 'https://graph.facebook.com/';
		$this->scopes   = array(
			'email',
			'pages_manage_posts',
			'pages_show_list',
			'publish_video',
			'pages_manage_metadata',
			'pages_manage_engagement',
		);
	}

	/**
	 * @throws Exception
	 */
	public function auth_link( array $scopes = array() ): string {

		$this->has_credentials( $this->credentials['client_id'], $this->credentials['client_secret'] );
		$scopes = array_merge( $this->scopes, $scopes );
		return add_query_arg(
			array(
				'client_id'     => $this->credentials['client_id'],
				'client_secret' => $this->credentials['client_secret'],
				'redirect_uri'  => $this->redirect_url(),
				'auth_type'     => 'rerequest',
				'config_id'     => $this->credentials['app_configuration'],
				'state'         => wp_create_nonce( 'nevamiss-facebook-secret' ),
				'scope'         => join( ',', $scopes ),

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
				'redirect_uri'  => $this->redirect_url(),
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
			throw new \Exception( esc_html( "Facebook App ID,{$this->credentials['client_id']} could not be verified" ) );
		}
		// Exchange short-live token for long-live token

		['access_token' => $access_token] = $this->long_live_token( $token['access_token'] );

		$user                     = $this->get_user( $user_data['data']['user_id'], $access_token );
		$user['access_token']     = $access_token;
		$user['network_label']    = 'Facebook';
		$user['token_expires_in'] = 60 * 60 * 24 * 60;

		$user['pages'] = $this->get_account( $access_token, $user['id'] );

		return $user;
	}

	public function get_account( string $access_token, string $user_id = null ) {
		return $this->user_pages( $user_id, $access_token );
	}

	public function post( array $data, Network_Account $account ) {
		$response = match ( true ) {
			( isset( $data['image_url'] ) && $data['image_url'] ) => $this->post_media( $data, $account ),
			default => $this->post_text( $data, $account )
		};
		return $response['id'];
	}

	/**
	 * Post image to a page.
	 *
	 * Find more about the parameters to this endpoint here: https://developers.facebook.com/docs/graph-api/reference/v20.0/photo#parameters-2
	 *
	 * @param array           $data
	 * @param Network_Account $account
	 * @return array|string
	 * @throws Exception
	 */
	public function post_media( array $data, Network_Account $account ) {
		$endpoint = "$this->root_url_versioned{$account->remote_account_id()}/photos";

		return $this->request->post(
			$endpoint,
			array_merge(
				$this->auth_header( $account->token() ),
				array(
					'body' => wp_json_encode(
						array(
							'caption' => $data['status_text'],
							'url'     => $data['link'],
						)
					),
				)
			)
		);
	}

	/**
	 * Post simple text to a page
	 *
	 * @param array           $data
	 * @param Network_Account $account
	 * @return array|string
	 * @throws Exception
	 */
	public function post_text( array $data, Network_Account $account ) {
		$endpoint = "$this->root_url_versioned{$account->remote_account_id()}/feed";

		return $this->request->post(
			$endpoint,
			array_merge(
				$this->auth_header( $account->token() ),
				array(
					'body' => wp_json_encode(
						array(
							'message' => $data['status_text'],
							'link'    => $data['link'],
						)
					),
				)
			)
		);
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
	 * @return array{access_token: string, expires_in: int, token_type:string}
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

	/**
	 * @return string|null
	 */
	private function redirect_url(): ?string {
		return admin_url( 'admin-post.php?action=facebook' );
	}
}
