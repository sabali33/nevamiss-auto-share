<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Url_Shortner;

use Exception;
use Nevamiss\Services\Http_Request;
use Nevamiss\Services\Settings;

class Rebrandly implements URL_Shortner_Interface {

	const REBRANDLY = 'rebrandly';
	private string $endpoint;

	public function __construct( private Http_Request $http_request, private Settings $settings ) {
		$this->endpoint = 'https://api.rebrandly.com/v1/links';
	}

	/**
	 * @param string $url
	 * @param array  $args
	 * @return Url_Shortner_Response
	 * @throws Exception
	 */
	public function create( string $url, array $args = array() ): URL_Shortner_Response {
		$api = $this->settings->url_shortner_credentials();

		$response = $this->http_request->post(
			$this->endpoint,
			array(
				'headers' => array(
					'accept'       => 'application/json',
					'apikey'       => $api['api_key'],
					'content-type' => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'destination' => $url,
						'title'       => $args['title'] ?? '',
						'hashtag'     => $args['hashtag'] ?? '',
						'domain'      => $args['domain'] ?? array(
							'id'       => null,
							'fullName' => '',
						),
					)
				),
			)
		);

		$status = 'active' === $response['status'];

		return new Url_Shortner_Response( $response['shortUrl'], $response['id'], $status, $response['isPublic'] );
	}

	/**
	 * @return string
	 */
	public function id(): string {
		return self::REBRANDLY;
	}

	/**
	 * @return mixed
	 */
	public function label() {
		return 'Rebrandly';
	}

	public function settings_fields( array $settings_values ): array {
		$url_shortner_clients = $settings_values['url_shortner_client'];

		return array(
			'name'       => 'url_shortner_client',
			'label'      => __( 'Enable Rebrandly', 'nevamiss' ),
			'type'       => 'checkbox',
			'value'      => 'rebrandly',
			'checked'    => 'rebrandly' === $url_shortner_clients,
			'class'      => 'parent-field',
			'sub_fields' => array(
				array(
					'name'     => 'rebrandly[api_key]',
					'label'    => __( 'Rebrandly Api key', 'nevamiss' ),
					'type'     => 'text',
					'value'    => $settings_values['rebrandly']['api_key'],
					'size'     => 30,
					'class'    => 'rebrandly-api',
					'disabled' => 'rebrandly' !== $url_shortner_clients,
				),
				array(
					'name'     => 'rebrandly[shortlink]',
					'label'    => __( 'Short Link', 'nevamiss' ),
					'type'     => 'text',
					'value'    => $settings_values['rebrandly']['shortlink'],
					'size'     => 30,
					'class'    => 'rebrandly-link',
					'disabled' => 'rebrandly' !== $url_shortner_clients,
				),
			),
		);
	}
}
