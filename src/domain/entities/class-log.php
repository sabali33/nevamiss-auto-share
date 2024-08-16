<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Entities;

class Log {
	private int $schedule_id;
	private int $id;
	private string $messages;

	private string $posted_on;

	public function __construct( array $log ) {
		$this->schedule_id    = (int) $log['schedule_id'];
		$this->messages = $log['messages'];
		$this->posted_on      = $log['created_at'];
		$this->id      = (int)$log['id'];
	}

	public function schedule_id(): int {
		return $this->schedule_id;
	}

	public function messages(): string {
		return $this->messages;
	}
	public function posted_on(): string {
		return $this->posted_on;
	}

	public function id(): int {
		return $this->id;
	}
}
