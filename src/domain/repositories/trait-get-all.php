<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

trait Get_All_Trait {

	/**
	 */
	public function get_all( array $options = array() ): array|object|null {
		[$where_clause, $data] = $this->where_clause( $options );
		$sql                   = "SELECT * FROM {$this->table_name()}";

		if ( $where_clause ) {
			$sql .= 'WHERE ' . $where_clause;
            $sql = $this->wpdb->prepare(
                $sql,
                ...$data
            );
		}

		return $this->wpdb->get_results( $sql, ARRAY_A );
	}

	private function where_clause( array $options ): array {
		$where_string = '';
		$data         = array();

		foreach ( $options as $key => $option ) {
			$where_string .= " $key= %s";
			$data[]        = $option;
		}
		return array( $where_string, $data );
	}
}
