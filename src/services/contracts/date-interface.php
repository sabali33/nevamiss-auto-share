<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

interface Date_Interface
{
    public function timestamp(string $date ): int;
    public function posting_time_in_week(array $week_days_time ): array;
    public function posting_time_in_month(array $dates ): array;

}