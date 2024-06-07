<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Delete_All_Interface;
use Nevamiss\Domain\Contracts\Delete_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;


class Posts_Stats_Repository implements Create_Interface, Delete_Interface, Get_All_Interface, Delete_All_Interface {

	use Repository_Common_Trait;
	use Create_Trait;
	use Delete_Trait;
	use Delete_All_Trait;
	use Get_All_Trait;

	private const ALLOWED_TABLE_COLUMNS = array(
		'schedule_id',
		'posted_on',
		'posts_ids',
		'cycles_count',
		'remote_posted',
		'status',
	);

	public function clear() {
	}

	private function table_name(): string {
		return "{$this->wpdb->prefix}_nevamiss_stats";
	}
}
