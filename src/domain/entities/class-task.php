<?php

namespace Nevamiss\Domain\Entities;

class Task {

	public function __construct(
		private int $id,
		private string $class_identifier,
		private array $parameters,
		private ?int $schedule,
		private ?string $status = 'pending',
	) {
	}

	public function id(): int {
		return $this->id;
	}

	public function class_identifier(): string {
		return $this->class_identifier;
	}

	public function parameters(): array {
		return $this->parameters;
	}

	public function status(): string {
		return $this->status;
	}

	public function schedule(): ?int {
		return $this->schedule;
	}
}
