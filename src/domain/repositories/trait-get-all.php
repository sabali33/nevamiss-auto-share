<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

trait Get_All_Trait {

	/**
	 */
	public function get_all( array $options = array() ): array {

		[$where_clause, $data] = $this->where_clause( $options );
		$limit                 = $this->limit_clause( $options );

		$sql = "SELECT * FROM {$this->table_name()}";

		if ( $where_clause ) {
			$sql .= ' WHERE ' . $where_clause;
			$sql  = $this->wpdb->prepare(
				$sql,
				...$data
			);
		}
		if ( $limit ) {
			$sql .= $limit;
		}

		$entities = $this->wpdb->get_results( $sql, ARRAY_A );
		if ( ! $entities ) {
			return array();
		}
		return $this->to_models( $entities );
	}

	private function where_clause( array $options ): array {

		if ( ! isset( $options['where'] ) ) {
			return array( null, null );
		}
		$where_string = array();
		$data         = array();

		foreach ( $options['where'] as $key => $option ) {
			if ( ! $option ) {
				continue;
			}

			$where_string[] = "$key= %s";
			$data[]         = $option;
		}

		$where_string = join( ' AND ', $where_string );

		return array( $where_string, $data );
	}

	public function limit_clause( array $options ): ?string {
		if ( ! isset( $options['per_page'] ) ) {
			return null;
		}
		return "LIMIT {$options['per_page']}";
	}
}
