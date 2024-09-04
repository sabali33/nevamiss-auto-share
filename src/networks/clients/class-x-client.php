<?php

declare(strict_types=1);

namespace Nevamiss\Networks\Clients;

use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Http_Request;
use Nevamiss\Services\Settings;
use Random\RandomException;

class X_Client implements Network_Clients_Interface {
	use Has_Credentials_Trait;
	private ?string $client_id;
	private ?string $client_secret;
	private string $redirect_url;
	private string $root_auth;
	private string $root_api;
	private string $upload_root_api;

	use Request_Parameter_Trait;

	public function __construct( private Http_Request $request, private Settings $settings, array $api_credentials ) {
		$this->client_id       = $api_credentials['client_id'] ?? null;
		$this->client_secret   = $api_credentials['client_secret'] ?? null;
		$this->redirect_url    = admin_url( 'admin-post.php?action=x' );
		$this->root_auth       = 'https://twitter.com/i/oauth2/authorize';
		$this->root_api        = 'https://api.twitter.com/2';
		$this->upload_root_api = 'https://upload.twitter.com/1.1';
	}


	/**
	 * @throws \Exception
	 */
	public function auth_link(): string {
		$this->has_credentials( $this->client_id, $this->client_secret );
		$code_challenge = $this->challenge_code();
		set_transient( 'nevamiss-x-code-challenge', $code_challenge, 60 * 60 );
		return add_query_arg(
			array(
				'response_type'         => 'code',
				'client_id'             => $this->client_id,
				'client_secret'         => $this->client_id,
				'redirect_uri'          => $this->redirect_url,
				'scope'                 => 'tweet.read%20tweet.write%20users.read%20offline.access',
				'state'                 => wp_create_nonce( 'nevamiss-x-secret' ),
				'code_challenge'        => $code_challenge,
				'code_challenge_method' => 'plain',
			),
			$this->root_auth
		);
	}

	/**
	 * @throws RandomException
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
	 * @throws \Exception
	 */
	public function auth( string $code ): array {

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
			'redirect_uri'  => $this->redirect_url,
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
			throw new \Exception( esc_html( $response['error_description'] ) );
		}
		return $this->get_account( $response['access_token'] );
	}

	/**
	 * @throws \Exception
	 */
	public function get_account( string $access_token, string $user_id=null ): array {
		$args = $this->auth_header( $access_token );

		$url = "$this->root_api/users/me";

		$response = $this->request->get( $url, $args );

		if ( isset( $response['errors'] ) ) {
			throw new \Exception( esc_html__( 'Unable to get user account', 'nevamiss' ) );
		}
		return array(
			'name'          => "{$response['data']['name']}({$response['data']['username']})",
			'id'            => $response['data']['id'],
			'access_token'  => $access_token,
			'network_label' => 'X',
		);
	}

	/**
	 * @throws \Exception
	 */
	public function post( array $data, Network_Account $account ) {
		// $media = $this->upload_media( $data['image_url'], $account->token() );
		// return;
		$args = $this->auth_header( $account->token() );

		$args['headers']['Accept'] = 'application/json';

		$args['body'] = wp_json_encode( array( 'text' => $data['status_text'] ) );
		$url          = "$this->root_api/tweets";

		$response = $this->request->post( $url, $args );

		if ( ! isset( $response['data']['id'] ) ) {
			throw new \Exception( esc_html( "Unable to share to {$account->name()} on {$account->network()}" ) );
		}
		return $response['data']['id'];
	}

	/**
	 * @throws \Exception
	 */
	private function upload_media( string $media_file_url, string $access_token ): array {
		$url = "$this->upload_root_api/media/upload.json?media_category=TWEET_IMAGE";
		// $url = parse_url($media_file_url);

		$args                            = $this->auth_header( $access_token );
		$args['headers']['Content-Type'] = 'application/octet-stream';
		$args['headers']['Accept']       = 'application/json';

		$args['body']    =
			array(
				// "name" => 'media',
				// "contents" => base64_encode(file_get_contents($media_file_url)),
						'media_data' => base64_encode( file_get_contents( $media_file_url ) ),
			);
		$args['timeout'] = 45;

		// $args['body'] = [
		// 'media_category' => 'tweet_image',
		// 'media' => file_get_contents($media_file_url),
		// 'media' => $this->url_to_binary($media_file_url),
		// ];

		return $this->request->post( $url, $args );
	}

	private function url_to_binary( string $media_file_url ): false|string {
		$handle   = fopen( $media_file_url, 'rb' );
		$contents = stream_get_contents( $handle );
		fclose( $handle );
		return $contents;
	}
}
