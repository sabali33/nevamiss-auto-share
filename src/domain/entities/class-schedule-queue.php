<?php

namespace Nevamiss\Domain\Entities;

class Schedule_Queue {

	public function __construct(
		private int $id,
		private int $schedule_id,
		private array $shared_posts_ids,
		private array $all_posts_ids,
	) {
	}

	public function schedule_id(): string {
		return $this->schedule_id;
	}

	public function id(): int {
		return $this->id;
	}
	public function shared_posts_ids(): array {
		return $this->shared_posts_ids;
	}
	public function all_posts_ids(): array {
		return $this->all_posts_ids;
	}
}
