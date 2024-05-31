<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Services\Contracts\Remote_Schedule_Interface;

class Remote_Schedule implements Remote_Schedule_Interface
{

    public function post(Schedule $schedule): void
    {
        // TODO: Implement post() method.
    }

    public function get(int $id): void
    {
        // TODO: Implement get() method.
    }

    public function update(Schedule $schedule): void
    {
        // TODO: Implement update() method.
    }

    public function delete(int $id): void
    {
        // TODO: Implement delete() method.
    }
}