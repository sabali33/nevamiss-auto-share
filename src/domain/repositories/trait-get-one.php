<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Application\Not_Found_Exception;

trait Get_One_Trait {

	/**
	 * @throws Not_Found_Exception
	 */
	public function get( int $id ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sql = $this->wpdb->prepare( "SELECT * FROM {$this->table_name()} WHERE id=%s", $id );

		$results = $this->wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( ! $results ) {
			throw new Not_Found_Exception( self::ENTITY_NAME . ' with the ID not found' );
		}
		[$entity] = $results;

		return $this->to_model( $entity );
	}
}
