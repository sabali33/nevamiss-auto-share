<?php

declare(strict_types=1);

namespace Nevamiss\Networks;

use Nevamiss\Networks\Contracts\Network_Clients_Interface;

class Media_Network_Collection {

	private array $networks = array();
	public function register( string $network_slug, Network_Clients_Interface $client ): void {
		$this->networks[ $network_slug ] = $client;
	}

	public function get( string $network_slug ) {
		return $this->networks[ $network_slug ];
	}
	public function get_all(): array
	{
		return $this->networks;
	}
}
