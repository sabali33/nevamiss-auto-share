<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Infrastructure\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Contracts\Remote_Post_Interface;

class Network_Post_Manager implements Remote_Post_Interface {

	public function __construct(
		private Network_Account $account,
		private Network_Clients_Interface $network_client,
	) {
	}

	public function post( array $data ): string {
		return $this->network_client->post( $data, $this->account );
	}
}
