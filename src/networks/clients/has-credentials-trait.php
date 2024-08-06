<?php

declare(strict_types=1);

namespace Nevamiss\Networks\Clients;

use Exception;

trait Has_Credentials_Trait {

	public function has_credentials( string $client_id, string $client_secret ): bool {
		if ( ! $client_id || ! $client_secret ) {
			throw new Exception( __( 'You need to set client ID and client secret.', 'nevamiss' ) );
		}
		return true;
	}
}
