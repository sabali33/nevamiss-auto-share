<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

interface Cron_Interface {

	/**
	 * @param int $schedule_id
	 * @return bool
	 */
	public function create_schedule(int $schedule_id): bool;
	public function update_schedule(): bool;
	public function delete_schedule(): bool;

	public function next_schedule( int $id ): int|false;
}
