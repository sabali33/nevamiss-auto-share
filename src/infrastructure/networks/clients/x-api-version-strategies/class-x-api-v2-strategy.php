<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Networks\Clients\X_Api_Version_Strategy;

use Exception;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Infrastructure\networks\clients\Has_Credentials_Trait;
use Nevamiss\Infrastructure\networks\clients\Request_Parameter_Trait;
use Nevamiss\Services\Http_Request;
use Throwable;
use function Nevamiss\sanitize_text_input_field;

class X_Api_V2_Strategy implements X_Api_Version_Strategy {

	private string $client_id;
	private string $client_secret;
	private string $root_auth;
	private string $root_api;
	private string $upload_root_api;

	use Has_Credentials_Trait;
	use Request_Parameter_Trait;

	public function __construct( private Http_Request $request, array $api_credentials ) {
		$this->client_id     = $api_credentials['client_id'];
		$this->client_secret = $api_credentials['client_secret'];
		$this->root_auth     = 'https://twitter.com/i/oauth2/authorize';
		$this->root_api      = 'https://api.twitter.com/2';
	}

	/**
	 * @param array|string $code
	 * @param string       $callback_url
	 * @return array
	 * @throws Exception
	 */
	public function auth( array|string $code, string $callback_url ) {
		$access_token_endpoint = "{$this->root_api}/oauth2/token";

		$code_challenge = get_transient( 'nevamiss-x-code-challenge' );

		$basic   = base64_encode( "$this->client_id:$this->client_secret" );
		$headers = array(
			'Content-Type'  => 'application/x-www-form-urlencoded',
			'Authorization' => "Basic $basic",
		);

		$body = array(
			'code'          => $code,
			'grant_type'    => 'authorization_code',
			'redirect_uri'  => $callback_url,
			'code_verifier' => $code_challenge,
		);

		$response = $this->request->post(
			$access_token_endpoint,
			array(
				'headers' => $headers,
				'body'    => $body,
			)
		);

		if ( isset( $response['error'] ) ) {
			throw new Exception( esc_html( $response['error_description'] ) );
		}

		return array_merge(
			$this->get_accounts( $response['access_token'] ),
			array(
				'token_expires_in' => $response['expires_in'],
				'refresh_token'    => $response['refresh_token'],
			)
		);
	}

	/**
	 * @return mixed
	 * @throws Exception
	 * @throws Throwable
	 */
	public function auth_link( string $callback_url ): string {
		$this->has_credentials( $this->client_id, $this->client_secret );
		$code_challenge = $this->challenge_code();
		set_transient( 'nevamiss-x-code-challenge', $code_challenge, 60 * 60 );
		return add_query_arg(
			array(
				'response_type'         => 'code',
				'client_id'             => $this->client_id,
				'client_secret'         => $this->client_id,
				'redirect_uri'          => $callback_url,
				'scope'                 => 'tweet.read%20tweet.write%20users.read%20offline.access',
				'state'                 => wp_create_nonce( 'nevamiss-x-secret' ),
				'code_challenge'        => $code_challenge,
				'code_challenge_method' => 'plain',
			),
			$this->root_auth
		);
	}

	public function verified_code(): string {
		return sanitize_text_input_field( 'code' );
	}

	/**
	 * @throws Throwable
	 */
	private function challenge_code(): string {
		$random_bytes = random_bytes( 30 );

		$code_verifier = str_replace(
			array( '+', '/', '=' ),
			array( '-', '_', '' ),
			base64_encode( $random_bytes )
		);

		$code_verifier  = preg_replace( '/[^a-zA-Z0-9]+/', '', $code_verifier );
		$code_challenge = hash( 'sha256', $code_verifier, true );

		return str_replace(
			array( '+', '/', '=' ),
			array( '-', '_', '' ),
			base64_encode( $code_challenge )
		);
	}

	/**
	 * @param string      $access_token
	 * @param string|null $user_id
	 * @return mixed
	 * @throws Exception
	 */
	public function get_accounts( string $access_token, string $user_id = null ): array {
		$args = $this->auth_header( $access_token );

		$url = "$this->root_api/users/me";

		$response = $this->request->get( $url, $args );

		if ( isset( $response['errors'] ) ) {
			throw new Exception( esc_html__( 'Unable to get user account', 'nevamiss' ) );
		}
		return array(
			'name'          => "{$response['data']['name']}({$response['data']['username']})",
			'id'            => $response['data']['id'],
			'access_token'  => $access_token,
			'network_label' => 'X',
		);
	}

	/**
	 * @param array           $data
	 * @param Network_Account $account
	 * @return mixed
	 * @throws Exception
	 */
	public function post( array $data, Network_Account $account ): mixed {
		$args = $this->auth_header( $account->token() );

		$args['headers']['Accept'] = 'application/json';

		$args['body'] = wp_json_encode( array( 'text' => $data['status_text'] ) );
		$url          = "$this->root_api/tweets";

		$response = $this->request->post( $url, $args );

		if ( ! isset( $response['data']['id'] ) ) {
			throw new Exception( esc_html( "Unable to share to {$account->name()} on {$account->network()}" ) );
		}
		return $response['data']['id'];
	}

	/**
	 * @param string $refresh_token
	 * @param string $basic
	 * @return array|string
	 * @throws Exception
	 */
	public function refresh_token( string $refresh_token, string $basic ): array|string {
		$data = array(
			'grant_type'    => 'refresh_token',
			'client_id'     => $this->client_id,
			'refresh_token' => $refresh_token,
		);

		return $this->request->post(
			'https://api.x.com/2/oauth2/token',
			array(
				'headers' => array(
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Authorization' => "Basic $basic",
				),
				'body'    => $data,
			)
		);
	}
}
