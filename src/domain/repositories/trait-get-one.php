<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Application\Not_Found_Exception;

trait Get_One_Trait {

	/**
	 * @throws Not_Found_Exception
	 */
	public function get( int $id ) {
		$sql = $this->wpdb->prepare( "SELECT * FROM {$this->table_name()} WHERE id='%s'", $id );

		[$entity] = $this->wpdb->get_results( $sql, ARRAY_A );

		if ( ! $entity ) {
			throw new Not_Found_Exception( self::ENTITY_NAME . ' with the ID not found' );
		}

		return $this->factory->new( self::ENTITY_CLASS, $entity );
	}
}
