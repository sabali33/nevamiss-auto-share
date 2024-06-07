<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Contracts\Update_Interface;
use Nevamiss\Domain\Entities\Schedule_Queue;

class Schedule_Queue_Repository implements Create_Interface, Get_One_Interface, Update_Interface {

	use Repository_Common_Trait;
	use Create_Trait;
	use Update_Trait;
	use Get_One_Trait;
	use Get_All_Trait;

	private const ENTITY_NAME           = 'Schedule Queue';
	private const ENTITY_CLASS          = Schedule_Queue::class;
	private const ALLOWED_TABLE_COLUMNS = array(
		'id',
		'schedule_id',
		'shared_posts_ids',
		'all_posts_ids',
	);

	private function table_name(): string {
		return "{$this->wpdb->prefix}_nevamiss_schedule_queue";
	}
}
