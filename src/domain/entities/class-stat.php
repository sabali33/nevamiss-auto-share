<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Entities;

class Stats {
	private int $schedule_id;
	private int $post_id;
	private string $remote_post_id;
	private bool $remote_posted;

	public function __construct( array $stat ) {
		$this->schedule_id    = (int) $stat['schedule_id'];
		$this->post_id        = (int) $stat['post_id'];
		$this->remote_post_id = $stat['remote_post_id'];
		$this->remote_posted  = (bool) $stat['remote_posted'];
	}

	public function schedule_id(): int {
		return $this->schedule_id;
	}
	public function post_id(): int {
		return $this->post_id;
	}
	public function remote_post_id(): string {
		return $this->remote_post_id;
	}
	public function remote_posted(): bool {
		return $this->remote_posted;
	}
}
