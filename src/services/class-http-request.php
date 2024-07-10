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
		$body = wp_remote_retrieve_body($response);

		if(!$body){
			throw new \Exception($response['response']['message']);
		}

		return json_decode($body, true);
	}

	/**
	 * @throws \Exception
	 */
	public function post(string $url, array $args): array
	{

		$response = wp_remote_post(
			$url,
			$args
		);
		if(is_wp_error($response)){
			throw new \Exception($response->get_error_message());
		}

		$body = wp_remote_retrieve_body($response);

		if(!$body && !in_array($response['response']['code'], [200, 201]) ){
			throw new \Exception($response['response']['message']);
		}

		return $body ? json_decode($body, true) : $response['response'];
	}

	/**
	 * @throws \Exception
	 */
	public function put(string $url, array $args)
	{
		$response = wp_remote_request(
			$url,
			wp_parse_args( $args, ['method' => 'PUT'])
		);
		if(is_wp_error($response)){
			throw new \Exception($response->get_error_message());
		}

		return json_decode(wp_remote_retrieve_body($response), true);
	}
}