<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

interface Logger_Interface {

	public function save(array $messages, int $schedule_id): void;
}
