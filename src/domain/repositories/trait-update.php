<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Exception;

trait Update_Trait {

	/**
	 * @throws Exception
	 */
	public function update( int $id, mixed $data ): bool {
		$updated    = $this->wpdb->update( $this->table_name(), $data, array( 'id' => $id ) );
		$model_slug = self::ENTITY_SLUG;
		do_action( "nevamiss_{$model_slug}_updated", $this->wpdb->last_result );

		return (bool) $updated;
	}
}
