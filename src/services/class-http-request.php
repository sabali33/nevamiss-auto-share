<?php

declare(strict_types=1);

namespace Nevamiss\Services;

class Http_Request {
	/**
	 * @throws \Exception
	 */
	public function get(string $url, array $args=[]): array
	{
		$response = wp_remote_get($url, $args);

		if(is_wp_error($response)){
			throw new \Exception($response->get_error_message());
		}
		return json_decode(wp_remote_retrieve_body($response), true);
	}

	/**
	 * @throws \Exception
	 */
	public function post(string $url, string $body, string $client_id, string $client_secret): array
	{
		$response = wp_remote_post(
			$url,
			array(
				'headers' => array(
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $client_secret ),
				),
				'method'  => 'POST',
				'timeout' => 45,
				'body'    => $body,
			)
		);
		if(is_wp_error($response)){
			throw new \Exception($response->get_error_message());
		}
		return json_decode(wp_remote_retrieve_body($response), true);
	}
}