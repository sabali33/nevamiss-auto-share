<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Exception;

trait Create_Trait {

	/**
	 * @throws Exception
	 */
	public function create( mixed $data ): bool {
		$this->validate_data( $data );

		[$columns, $values] = $this->format_create_data( $data );

		$sql = $this->wpdb->prepare( "INSERT INTO {$this->table_name()} ($columns) VALUES ($values)", $data );

		$result = $this->wpdb->query( $sql );

		return (bool) $result;
	}
}
