<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Exception;

trait Create_Trait {

	/**
	 * @throws Exception
	 */
	public function create( mixed $data ): void {

		$model_slug =  self::ENTITY_SLUG;

		[$columns, $values] = $this->format_create_data( $data );
		$placeholder        = array_pad( array(), count( $values ), '%s' );
		$placeholder        = join( ',', $placeholder );

		$sql = $this->wpdb->prepare( "INSERT INTO {$this->table_name()} ($columns) VALUES ($placeholder)", ...$values );

		$results = $this->wpdb->query( $sql );

		if(!$results){
			return;
		}
		do_action( "nevamiss_created_$model_slug", $this->wpdb->insert_id );
	}
}
