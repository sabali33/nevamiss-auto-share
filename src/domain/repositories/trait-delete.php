<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Exception;

trait Delete_Trait {

	/**
	 * @throws Exception
	 */
	public function delete( int $id ): bool {
		$sql = $this->wpdb->prepare( "DELETE FROM {$this->table_name()} WHERE id='%s'", $id );

		$entity = $this->wpdb->query( $sql );

		if ( ! $entity ) {
			$entity_name = self::ENTITY_NAME;
			throw new Exception( "Unable to delete $entity_name with the ID $id" );
		}
		return true;
	}
}
