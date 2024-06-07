<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Delete_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Contracts\Update_Interface;
use Nevamiss\Domain\Entities\Schedule;

class Schedule_Repository implements Create_Interface, Get_One_Interface, Get_All_Interface, Update_Interface, Delete_Interface {

	use Repository_Common_Trait;
	use Create_Trait;
	use Update_Trait;
	use Get_One_Trait;
	use Delete_Trait;
	use Get_All_Trait;

	private const ENTITY_NAME         = 'Schedule';
	private const ENTITY_CLASS        = Schedule::class;
	private const ALLOW_TABLE_COLUMNS = array(
		'id',
		'name',
		'start_date',
		'repeat_frequency',
		'daily_times',
		'weekly_times',
		'monthly_times',
		'query',
		'accounts',
	);

	public function get_total(): int {
		return 2;
	}

	private function table_name(): string {
		return "{$this->wpdb->prefix}_nevamiss_schedules";
	}
}
