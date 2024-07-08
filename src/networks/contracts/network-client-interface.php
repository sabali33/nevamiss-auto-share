<?php

declare(strict_types=1);

namespace Nevamiss\Networks\Contracts;

use Nevamiss\Domain\Entities\Network_Account;

interface Network_Clients_Interface {

	public function auth_link();
	public function auth(string $code);
	public function get_account(string $access_token);
	public function post( string $data, Network_Account $account );
}
