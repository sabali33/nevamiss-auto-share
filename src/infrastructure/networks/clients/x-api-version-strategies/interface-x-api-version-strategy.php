<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Networks\Clients\X_Api_Version_Strategy;

use Nevamiss\Domain\Entities\Network_Account;

interface X_Api_Version_Strategy
{
	public function auth(array|string $code, string $callback_url);
	public function auth_link(string $callback_url);

	public function verified_code();
	public function post( array $data, Network_Account $account);
}