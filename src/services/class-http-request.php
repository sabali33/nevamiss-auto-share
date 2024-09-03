<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Exception;

class Http_Request {
	/**
	 * @throws Exception
	 */
	public function get( string $url, array $args = array() ): array {
		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			throw new Exception( esc_html( $response->get_error_message() ) );
		}
		$body = wp_remote_retrieve_body( $response );

		if ( ! $body ) {
			throw new Exception( esc_html( $response['response']['message'] ) );
		}
		$this->validate_response( $body, $response['response'] );

		return json_decode( $body, true );
	}

	/**
	 * @throws Exception
	 */
	public function post( string $url, array $args ): array|string {

		$response = wp_remote_post(
			$url,
			$args
		);
		if ( is_wp_error( $response ) ) {
			throw new Exception( esc_html( $response->get_error_message() ) );
		}

		$body = wp_remote_retrieve_body( $response );

		$this->validate_response( $body, $response['response'] );

		if ( ! $body && in_array( $response['response']['code'], array( 200, 201 ) ) ) {
			$headers = $response['headers']->getAll();

			return $headers['x-restli-id'];
		}

		return json_decode( $body, true );
	}

	/**
	 * @throws Exception
	 */
	public function put( string $url, array $args ) {
		$response = wp_remote_request(
			$url,
			wp_parse_args( $args, array( 'method' => 'PUT' ) )
		);
		if ( is_wp_error( $response ) ) {
			throw new Exception( esc_html( $response->get_error_message() ) );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * @param string    $body
	 * @param $response1
	 * @return void
	 * @throws Exception
	 */
	private function validate_response( string $body, $response ): void {
		if ( ! $body && ! in_array( $response['code'], array( 200, 201 ) ) ) {
			throw new Exception( esc_html( $response['message'] ) );
		}
		if ( ! in_array( $response['code'], array( 200, 201, 202, 204 ) ) ) {
			throw new Exception( 'Unable to successfully make the request: ' . $body );
		}
	}
}
