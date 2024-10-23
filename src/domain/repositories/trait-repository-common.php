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
	public function allowed_data( array $data ): array {
		$validated_data = array();

		foreach ( $data as $key => $value ) {

			if ( ! in_array( $key, self::ALLOW_TABLE_COLUMNS, true ) ) {
				continue;
			}

			$validated_data[ $key ] = $value;
		}
		return $validated_data;
	}
}
