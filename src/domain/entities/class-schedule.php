<?php

namespace Nevamiss\Domain\Entities;

class Schedule {

	private int $id;
	private string $schedule_name;
	private string $repeat_frequency;
	private array $network_accounts;
	private array $query_args;
	private ?string $social_media_tags;
	private ?array $daily_times;
	private ?array $monthly_times;
	private ?array $weekly_times;
	private ?array $one_time_schedule;
	private ?string $start_date;

	public function __construct(
		array $schedule
	) {
		$this->id = $schedule['id'];
		$this->schedule_name = $schedule['schedule_name'];
		$this->start_date = $schedule['start_date'];
		$this->repeat_frequency = $schedule['repeat_frequency'];
		$this->network_accounts = array_map('intval', json_decode($schedule['network_accounts'], true));
		$this->query_args = json_decode($schedule['query_args'], true);
		$this->social_media_tags = $schedule['social_media_tags'];
		$this->daily_times = $schedule['daily_times'] ?
			$this->to_numeric(json_decode($schedule['daily_times'], true)) :
			null;
		$this->monthly_times = $schedule['monthly_times'] ?
			$this->to_numeric(json_decode($schedule['monthly_times'], true)) :
			null;
		$this->weekly_times = $schedule['weekly_times'] ?
			$this->to_numeric(json_decode($schedule['weekly_times'], true)) :
			null;
		$this->one_time_schedule = $schedule['one_time_schedule'] ?
			json_decode($schedule['one_time_schedule']) : null;
	}

	public function post_data(): array {
		return array();
	}
	private function to_numeric(array $times): array
	{
		return array_map(function($time){
			return array_map(function($unit){
				if(is_numeric($unit)){
					return intval($unit);
				}
				return $unit;
			}, $time);
		}, $times);
	}

	public function is_heavy(): bool {
		$total_posting_required_at_once = count($this->network_accounts()) * $this->query_args()['posts_per_page'];
		return $total_posting_required_at_once > 6;
	}

	public function id(): int {
		return $this->id;
	}
	public function name(): string {
		return $this->schedule_name;
	}
	public function start_date(): string {
		return $this->start_date;
	}
	public function repeat_frequency(): string {
		return $this->repeat_frequency;
	}
	public function network_accounts(): array {
		return $this->network_accounts;
	}
	public function query_args(): array {
		return $this->query_args;
	}
	public function daily_times(): ?array {
		return $this->daily_times;
	}
	public function weekly_times(): ?array {
		return $this->weekly_times;
	}
	public function monthly_times(): ?array {
		return $this->monthly_times;
	}

	public function one_time_schedule(): ?array {
		return $this->one_time_schedule;
	}

	public function social_media_tags(): string {
		return $this->social_media_tags ?? '';
	}
}
