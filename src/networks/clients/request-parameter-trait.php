<?php

declare(strict_types=1);

namespace Nevamiss\Networks\Clients;

trait Request_Parameter_Trait {
	private function auth_header( string $access_token ): array {
		return array(
			'headers' => array(
				'X-Restli-Protocol-Version' => '2.0.0',
				'Authorization'             => "Bearer {$access_token}",
				'Content-Type'              => 'application/json',
				'x-li-format'               => 'json',
			),
		);
	}
}
