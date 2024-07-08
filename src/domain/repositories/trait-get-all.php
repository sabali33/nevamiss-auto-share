<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

trait Get_All_Trait {

	/**
	 */
	public function get_all( array $options = array() ): array {
		[$where_clause, $data] = $this->where_clause( $options );
		$sql                   = "SELECT * FROM {$this->table_name()}";

		if ( $where_clause ) {
			$sql .= ' WHERE ' . $where_clause;
			$sql  = $this->wpdb->prepare(
				$sql,
				...$data
			);
		}

		$entities = $this->wpdb->get_results( $sql, ARRAY_A );
		if(!$entities){
			return [];
		}
		return $entities;
	}

	private function where_clause( array $options ): array {

		if(!isset($options['where'])){
			return [];
		}
		$where_string = '';
		$data         = array();

		foreach ( $options['where'] as $key => $option ) {
			if ( ! $option ) {
				continue;
			}
			$where_string .= " $key= %s";
			$data[]        = $option;
		}
		return array( $where_string, $data );
	}
}
