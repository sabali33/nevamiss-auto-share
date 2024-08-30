<?php

namespace Nevamiss\Domain\Entities;

class Task {

	public function __construct(
		private array $task
	) {
	}

	public function id(): int {
		return $this->task['id'];
	}

	public function class_identifier(): string {
		return $this->task['class_identifier'];
	}

	public function parameters(): array {
		return json_decode( $this->task['parameters'], true );
	}

	public function status(): string {
		return $this->task['status'];
	}

	public function schedule(): ?int {
		return $this->task['schedule'];
	}
}
