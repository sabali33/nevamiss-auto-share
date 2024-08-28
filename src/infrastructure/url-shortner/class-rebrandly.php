<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Url_Shortner;

use Exception;
use Nevamiss\Services\Http_Request;
use Nevamiss\Services\Settings;

class Rebrandly implements URL_Shortner_Interface
{
	const REBRANDLY = 'rebrandly';

	public function __construct(private Http_Request $http_request, private Settings $settings)
	{
		$this->endpoint = 'https://api.rebrandly.com/v1/links';
	}

	/**
	 * @param string $url
	 * @param array $args
	 * @return Url_Shortner_Response
	 * @throws Exception
	 */
	public function create(string $url, array $args = []): URL_Shortner_Response
	{
		$api = $this->settings->url_shortner_credentials();

		$response = $this->http_request->post(
			$this->endpoint,
			array(
				'headers' => [
					'accept' => 'application/json',
					'apikey' => $api['key'],
					'content-type' => 'application/json',
				],
				'body' => [
					'destination' => $url,
					'title' => $args['title'] ?? '',
					'hashtag' => $args['hashtag'] ?? '',
					'domain' => $args['domain'] ?? ['id' => null, 'fullName' => ''],
				]
			)
		);
		return new Url_Shortner_Response($response['shortUrl'], $response['id'], $response['status'], $response['isPublic']);
	}

	/**
	 * @return string
	 */
	public function id(): string
	{
		return self::REBRANDLY;
	}

	/**
	 * @return mixed
	 */
	public function label()
	{
		return 'Rebrandly';
	}
}