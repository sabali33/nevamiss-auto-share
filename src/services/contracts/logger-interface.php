<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

interface Logger_Interface {

	public function save(string $message, int $schedule_id): void;
}
