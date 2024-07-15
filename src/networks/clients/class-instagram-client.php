<?php

declare(strict_types=1);

namespace Nevamiss\Networks\Clients;

use Nevamiss\Networks\Contracts\Network_Clients_Interface;

class Instagram_Client implements Network_Clients_Interface {


	public function auth_link() {
		// TODO: Implement auth_link() method.
	}

	public function auth( string $code ) {
		// TODO: Implement auth() method.
	}

	public function get_account( string $access_token ) {
		// TODO: Implement get_account() method.
	}

	public function post( array $data, mixed $account ) {
		// TODO: Implement post() method.
	}
}
