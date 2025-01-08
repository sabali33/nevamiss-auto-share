<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

interface Task_Runner_Interface {

	public function run( int $task_id ): bool;
}
