<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Application\Not_Found_Exception;
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
	private const ENTITY_SLUG         = 'schedule_queue';
	private const ALLOWED_TABLE_COLUMNS = array(
		'id',
		'schedule_id',
		'shared_posts_ids',
		'all_posts_ids',
	);

	/**
	 * @param int $schedule_id
	 * @return Schedule_Queue
	 * @throws Not_Found_Exception|\Exception
	 */
	public function get_schedule_queue_by_schedule_id(int $schedule_id): Schedule_Queue|false
	{
		$sql = $this->wpdb->prepare("SELECT * FROM {$this->table_name()} WHERE schedule_id='%s'", $schedule_id);

		$results = $this->wpdb->get_results($sql, ARRAY_A);

		if($results === null){
			throw new \Exception($this->wpdb->last_error);
		}
		if( empty($results ) ){
			return false;
		}
		return $this->factory->new(Schedule_Queue::class, $results[0]);
	}

	private function table_name(): string {
		return "{$this->wpdb->prefix}nevamiss_schedule_queue";
	}
}
