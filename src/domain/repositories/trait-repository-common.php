<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Domain\Factory\Factory;

trait Repository_Common_Trait {
	public function __construct( private Factory $factory, private \wpdb $wpdb ) {
	}

	/**
	 * @throws \Exception
	 */
	public function validate_data( array $data ): array {
		$validated_data = array();
		foreach ( $data as $key => $value ) {
			if ( ! in_array( $key, self::ALLOW_TABLE_COLUMNS ) ) {
				throw new \Exception( 'Invalid column' );
			}
			$validated_data[ $key ] = $value;
		}
		return $validated_data;
	}

	public function format_create_data( array $data ): array {
		$columns = implode( ',', array_keys( $data ) );
		$values  = implode( ',', array_values( $data ) );

		return array( $columns, $values );
	}
}
