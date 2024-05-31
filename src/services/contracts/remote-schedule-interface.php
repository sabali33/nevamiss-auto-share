<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

use Nevamiss\Domain\Entities\Schedule;

interface Remote_Schedule_Interface
{
    public function post(Schedule $schedule): void;
    public function get(int $id): void;
    public function update(schedule $schedule): void;
    public function delete(int $id): void;
}