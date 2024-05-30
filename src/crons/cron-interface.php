<?php

declare(strict_types=1);

namespace Nevamiss\Crons;

interface Cron_Interface
{
    public function create_schedule(): bool;
    public function update_schedule(): bool;
    public function delete_schedule(): bool;

    public function schedule(mixed $id): bool;
}