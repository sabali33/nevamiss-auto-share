<?php

declare(strict_types=1);

namespace Nevamiss\Networks\Clients;

use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Http_Request;

class Instagram_Client implements Network_Clients_Interface {

	use Has_Credentials_Trait;
	use Request_Parameter_Trait;

	private string $access_token_endpoint;

	public function __construct( private Http_Request $request, private array $credentials ) {
		$this->root_auth = 'https://www.instagram.com/oauth/authorize';
		$this->access_token_endpoint = 'https://api.instagram.com/oauth/access_token';
		$this->root_api = "https://graph.instagram.com/v20.0/";
	}
	public function auth_link() {
		$this->has_credentials( $this->credentials['client_id'], $this->credentials['client_secret'] );

		return add_query_arg([
			'enable_fb_login' => 0,
			'force_authentication' => 0,
			'client_id' => $this->credentials['client_id'],
			'redirect_uri' => $this->redirect_url(),
			'response_type' => 'code',
			'state'         => wp_create_nonce( 'nevamiss-instagram-secret' ),
			'scope' => 'business_basic,business_content_publish,business_manage_comments,business_manage_messages'
		], $this->root_auth);
	}

	/**
	 * @throws \Exception
	 */
	public function auth(string $code ) {
		$parameters = [
			'client_id' => $this->credentials['client_id'],
			'client_secret' => $this->credentials['client_secret'],
			'grant_type' => 'authorization_code',
			'redirect_uri' => $this->redirect_url(),
			'code' => $code
		];

		/**
		 * @var array{data:<array{access_token: string, user_id: string, permissions: array}>} $access_token
		 */
		$access_token = $this->request->post($this->access_token_endpoint, [
			'body' => $parameters
		]);

		$this->user_id = $access_token['user_id'];
		$long_live_access_token = $this->long_live_access_token($access_token['access_token']);
		$data = array( 'access_token' => $long_live_access_token['access_token']);
		$data = array_merge($data, $this->get_account($long_live_access_token['access_token']));
		$data['network_label'] = 'Instagram';

		return $data;
	}

	public function get_account( string $access_token) {

		$endpoint = add_query_arg([
			'access_token' => $access_token,
			'fields' => 'id,name,username',
		], "$this->root_api{$this->user_id}");

		$respo = $this->request->get($endpoint);
		return $respo;
	}

	public function post( array $data, Network_Account $account ) {
		$container = $this->media_container($account, $data['image_url']);

		$media = $this->publish_media($container, $account);
		return $media['id'];
	}

	public function redirect_url(): ?string
	{
		return admin_url('admin-post.php');
	}

	/**
	 * @param string $exchanged_code
	 * @return array{access_token: string, token_type: string, expires_in: int}
	 * @throws \Exception
	 */
	public function long_live_access_token(string $exchanged_code): array
	{
		$endpoint = add_query_arg([
			'grant_type' => 'ig_exchange_token',
			'client_secret' => $this->credentials['client_secret'],
			'access_token' => $exchanged_code
		], 'https://graph.instagram.com/access_token');

		return $this->request->get($endpoint);
	}

	public function refresh_long_live_token(string $access_token)
	{
		$endpoint = add_query_arg([
			'grant_type' =>  'ig_refresh_token',
			'access_token' => $access_token
		], 'https://graph.instagram.com/refresh_access_token');

		return $this->request->get($endpoint);
	}

	/**
	 * @param Network_Account $account
	 * @param string $image
	 * @return void
	 * @throws \Exception
	 */
	private function media_container(Network_Account $account, string $image): string
	{
		$endpoint = "$this->root_api{$account->remote_account_id()}/media";
		$response = $this->request->post(
			$endpoint,
			[
				'headers' => array(
					'Content-Type'              => 'application/json',
					'Authorization'             => "Bearer {$account->token()}",
				),
				'body' => wp_json_encode(array(
					'image_url' => $image
				))
			]
		);
		return $response['id'];
	}

	public function publish_media(string $container_id, Network_Account $account)
	{
		$endpoint = "$this->root_api{$account->remote_account_id()}/media_publish";
		return $this->request->post(
			$endpoint,
			array(
				'headers' => array(
					'Content-Type'              => 'application/json',
					'Authorization'             => "Bearer {$account->token()}",
				),
				'body' => array(
					'creation_id'=> $container_id
				)
			)
		);
	}
}
