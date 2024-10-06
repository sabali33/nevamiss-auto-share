<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Networks\Clients\X_Api_Version_Strategy;

use Exception;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Services\Date;
use Nevamiss\Services\Http_Request;
use Nevamiss\Services\Settings;

class X_Api_V1_Strategy implements X_Api_Version_Strategy {

	const X_V_1_TOKEN       = 'nevamiss-x-v1-token';
	const API_ROOT          = 'https://api.x.com/';
	const UPLOAD_MEDIA_ROOT = 'https://upload.twitter.com/1.1';
	private string $upload_root_api;
	private string $root_api;

	public function __construct( private Http_Request $request, private Settings $settings, private array $credentials ) {
		$this->upload_root_api = self::UPLOAD_MEDIA_ROOT;
		$this->root_api        = self::API_ROOT;
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	public function auth( array|string $code, string $callback_url ): array {
		$response = $this->request->post(
			"{$this->root_api}oauth/access_token",
			array(
				'body' => array(
					'oauth_consumer_key' => $this->credentials['api_key'],
					'oauth_token'        => $code['oauth_token'],
					'oauth_verifier'     => $code['oauth_verifier'],
				),
			)
		);
		$data     = $this->format_response( $response, '&' );

		$this->save_oauth_token(
			array(
				'oauth_token_secret' => $data['oauth_token_secret'],
			),
			false
		);

		return array(
			'name'                => $data['screen_name'],
			'id'                  => $data['user_id'],
			'access_token'        => $data['oauth_token'],
			'refresh_token'       => null,
			'token_expires_in'    => 'never',
			'network_label'       => 'X',
			'access_token_secret' => $data['oauth_token_secret'],
		);
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	public function auth_link( string $callback_url ): string {
		$endpoint   = "{$this->root_api}oauth/request_token";
		$headerAuth = $this->authorization_header(
			$endpoint,
			array(
				'oauth_callback' => $callback_url,
			)
		);

		$response           = $this->request->post(
			$endpoint,
			array(
				'headers' => array(
					'Authorization' => $headerAuth,
					'Accept'        => 'application/json',
				),
			)
		);
		$formatted_response = $this->format_response( $response );

		return add_query_arg(
			array(
				'oauth_token'    => $formatted_response['oauth_token'],
				'oauth_callback' => $this->url_encode( $callback_url ),
			),
			"{$this->root_api}oauth/authorize"
		);
	}

	public function verified_code(): array {
		return array(
			'oauth_token'    => filter_input( INPUT_GET, 'oauth_token' ),
			'oauth_verifier' => filter_input( INPUT_GET, 'oauth_verifier' ),
		);
	}

	/**
	 * @param array           $data
	 * @param Network_Account $account
	 * @return string
	 * @throws Exception
	 */
	public function post( array $data, Network_Account $account ): string {

		$media = $this->upload_media( $data, $account );

		$endpoint = "{$this->root_api}1.1/statuses/update.json";
		$tweet    = $this->url_encode( $data['status_text'] );

		$auth_headers = $this->authorization_header(
			$endpoint,
			array(
				'oauth_token' => $account->token(),
				'status'      => $tweet,
				'media_ids'   => array( $media ),
			)
		);

		$response = $this->request->post(
			$endpoint,
			array(
				'headers' => array(
					'Authorization' => $auth_headers,
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/x-www-form-urlencoded',
				),
				'body'    => http_build_query(
					array(
						'status'    => $tweet,
						'media_ids' => array( $media ),
					)
				),
			)
		);

		return $response['id_str'];
	}

	/**
	 * @param string $request_url
	 * @param array  $data {callback_url:string, sign_method:string, version:float, request_url:string, nonce:string}
	 * @return string
	 */
	private function sign( string $request_url, array $data ): string {

		$encoded = $this->url_encode( http_build_query( $data ) );

		$basestring = 'POST&' . $this->url_encode( $request_url ) . '&' . $encoded;

		$token_secret = ! isset( $data['oauth_callback'] ) ? $this->url_encode( $this->credentials['oauth_token_secret'] ) : '';

		$hashkey = $this->url_encode( $this->credentials['api_secret'] ) . '&' . $token_secret;

		return base64_encode( hash_hmac( 'sha1', $basestring, $hashkey, true ) );
	}
	public function get_field_string( $fields ): string {
		$field_string = '';
		foreach ( (array) $fields as $key => $field ) {
			$key           = $this->url_encode( $key );
			$value         = $this->url_encode( (string) $field );
			$field_string .= "$key=\"$value\", ";
		}
		return rtrim( $field_string, ', ' );
	}

	public function url_encode( array|string $item ): string|array {
		$output = '';
		if ( is_array( $item ) ) {
			$output = array_map(
				array( $this, 'url_encode' ),
				$item,
			);
		} elseif ( is_scalar( $item ) ) {
			$output = rawurlencode( $item );
		}
		return $output;
	}

	/**
	 * @param array $response
	 * @param bool  $cache
	 * @return void
	 */
	private function save_oauth_token( array $response, bool $cache = true ): void {
		if ( $cache ) {
			unset( $response['callback_confirmed'] );
			set_transient( self::X_V_1_TOKEN, $response, 60 * 60 );
			return;
		}

		$settings = $this->settings->network_credentials( 'x' );

		$this->settings->update( 'x', array_merge( $settings, $response ), 'network_api_keys' );
	}

	/**
	 * @param array|string $response
	 * @param string       $sep
	 * @return mixed
	 */
	private function format_response( array|string $response, string $sep = 'oauth_' ): mixed {
		$token_arr = explode( $sep, trim( $response ) );

		return array_reduce(
			$token_arr,
			function ( array $acc, $item ) use ( $sep ) {
				if ( ! $item ) {
					return $acc;
				}
				$item_arr    = explode( '=', $item );
				$key         = $sep === 'oauth_' ? "oauth_$item_arr[0]" : $item_arr[0];
				$acc[ $key ] = $item_arr[1];
				return $acc;
			},
			array()
		);
	}

	/**
	 * @param string $endpoint
	 * @param array  $params
	 * @return string
	 * @throws Exception
	 */
	private function authorization_header( string $endpoint, array $params = array() ): string {

		$nonce     = wp_create_nonce( 'nevamiss-x-secret' );
		$timestamp = Date::now()->timestamp();

		$params = array_merge(
			array(
				'oauth_consumer_key'     => $this->credentials['api_key'],
				'oauth_nonce'            => $nonce,
				'oauth_signature_method' => 'HMAC-SHA1',
				'oauth_timestamp'        => $timestamp,
				'oauth_version'          => '1.0',
			),
			$params
		);

		$params = $this->sort_params( $params );

		$params['oauth_signature'] = $this->sign( $endpoint, $params );

		unset( $params['media'] );
		unset( $params['status'] );
		return 'OAuth ' . $this->get_field_string( $params );
	}

	private function sort_params( $params ) {
		uksort( $params, 'strcmp' );
		return $params;
	}

	/**
	 * @param array           $data
	 * @param Network_Account $account
	 * @return array|string
	 * @throws Exception
	 */
	private function upload_media( array $data, Network_Account $account ): string|false {
		if ( ! isset( $data['image_url'] ) ) {
			return false;
		}

		$endpoint = "$this->upload_root_api/media/upload.json";

		$image_data = $this->request->get( $data['image_url'] );

		if ( ! $image_data ) {
			throw new Exception( 'Unable read from image url' );
		}

		$filedata = base64_encode( $image_data );

		$authorized_header = $this->authorization_header(
			$endpoint,
			array(
				'oauth_token' => $account->token(),
				'media'       => $filedata,
			)
		);

		$response = $this->request->post(
			$endpoint,
			array(
				'headers' => array(
					'Authorization' => $authorized_header,
					'Accept: application/json',
				),
				'body'    => array(
					'media' => $filedata,
				),
			)
		);

		return $response['media_id_string'];
	}
}
