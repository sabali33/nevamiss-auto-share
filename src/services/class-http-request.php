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
			throw new Exception( esc_html( $response->get_error_message() ));
		}
		$body = wp_remote_retrieve_body( $response );

		if ( ! $body ) {
			throw new Exception( esc_html( $response['response']['message'] ));
		}

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
			throw new Exception( esc_html( $response->get_error_message() ));
		}

		$body = wp_remote_retrieve_body( $response );

		if ( ! $body && ! in_array( $response['response']['code'], array( 200, 201 ) ) ) {
			throw new Exception( esc_html( $response['response']['message'] ));
		}

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
			throw new Exception( esc_html( $response->get_error_message()) );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}
