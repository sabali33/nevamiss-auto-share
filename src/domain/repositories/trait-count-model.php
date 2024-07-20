<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Application\Not_Found_Exception;

trait Count_Model_Trait {

	public function get_total(): int {
		$sql = "SELECT COUNT(*) FROM {$this->table_name()}";
		[$count] = $this->wpdb->get_results($sql, ARRAY_N);
		return intval($count[0]);
	}
}
