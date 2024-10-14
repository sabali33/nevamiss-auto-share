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

		$this->wpdb->suppress_errors = true;

		$this->wpdb->insert( $this->table_name(), $data );

		if ( $this->wpdb->last_error ) {
			throw new \Exception( esc_html( $this->wpdb->last_error ) );
		}

		do_action( "nevamiss_created_$model_slug", $this->wpdb->insert_id );

		return $this->wpdb->insert_id;
	}
}
