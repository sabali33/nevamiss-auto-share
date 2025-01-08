<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

interface Cron_Interface {

	/**
	 * @param int $schedule_id
	 * @return bool
	 */
	public function create_cron( int $schedule_id ): bool;
	public function unschedule( int $schedule_id ): int;

	public function next_schedule( int $id ): int|false;
}
