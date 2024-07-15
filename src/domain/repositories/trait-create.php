<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Exception;

trait Create_Trait {

	/**
	 * @throws Exception
	 */
	public function create( mixed $data ): int {

		$model_slug = self::ENTITY_SLUG;

		[$columns, $values] = $this->format_create_data( $data );
		$placeholder        = array_pad( array(), count( $values ), '%s' );
		$placeholder        = join( ',', $placeholder );

		$sql                         = $this->wpdb->prepare( "INSERT INTO {$this->table_name()} ($columns) VALUES ($placeholder)", ...$values );
		$this->wpdb->suppress_errors = true;
		$this->wpdb->query( $sql );

		if ( $this->wpdb->last_error ) {
			throw new \Exception( $this->wpdb->last_error );

		}
		do_action( "nevamiss_created_$model_slug", $this->wpdb->insert_id );

		return $this->wpdb->insert_id;
	}
}
