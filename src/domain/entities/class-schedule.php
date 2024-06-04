<?php

namespace Nevamiss\Domain\Entities;

class Schedule
{
    public function __construct(
        private int    $id,
        private string $name,
        private string $start_date,
        private string $repeat_frequency,
        private int    $share_posts_count,
        private array  $network_accounts,
        private array  $query_args,
        private ?array $daily_times,
        private ?array $weekly_times,
        private ?array $monthly_times,
    )
    {
    }

    public function post_data(): array
    {
        return [];
    }

    public function is_heavy(): bool
    {
        return true;
    }

    public function id(): int
    {
        return $this->id;
    }
    public function name(): string
    {
        return $this->name;
    }
    public function start_date(): string
    {
        return $this->start_date;
    }
    public function repeat_frequency(): string
    {
        return $this->repeat_frequency;
    }
    public function share_posts_count(): int
    {
        return $this->share_posts_count;
    }
    public function network_accounts(): array
    {
        return $this->network_accounts;
    }
    public function query_args(): array
    {
        return $this->query_args;
    }
    public function daily_times(): ?array
    {
        return $this->daily_times;
    }
    public function weekly_times(): ?array
    {
        return $this->weekly_times;
    }
    public function monthly_times(): ?array
    {
        return $this->monthly_times;
    }
}