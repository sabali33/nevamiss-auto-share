<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Exception;

trait Update_Trait {

	/**
	 * @throws Exception
	 */
	public function update( int $id, mixed $data ): bool {
		$validated_data = $this->validate_data( $data );

		$updated = $this->wpdb->update( $this->table_name(), $validated_data, array( 'id' => $id ) );
		return (bool) $updated;
	}
}
