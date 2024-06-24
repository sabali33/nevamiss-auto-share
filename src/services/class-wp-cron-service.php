<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Services\Contracts\Cron_Interface;

class WP_Cron_Service implements Cron_Interface {
	public function __construct(private Schedule_Repository $schedule_repository)
	{
	}

	public function add_cron(array $schedules): array
	{
		$schedules['monthly'] = array(
			'interval' =>  (60 * 60 * 24 * 7),
			'display'  => esc_html__( 'Monthly', 'nevamiss' ),
		);
		return $schedules;
	}

	/**
	 * @throws Not_Found_Exception
	 * @throws \Exception
	 */
	public function create_schedule(int $schedule_id): bool {
		/**
		 * @var Schedule $schedule
		 */
		$schedule = $this->schedule_repository->get($schedule_id);


		if($schedule->one_time_schedule()){
			$dates = $this->to_date($schedule->one_time_schedule());
			$this->schedule_one_cron($dates, $schedule_id);
			return true;
		}

		if($schedule->repeat_frequency() === 'daily'){
			$timestamps = $this->daily_times($schedule->start_date(), $schedule->daily_times());
			$this->schedule_cron($timestamps, $schedule_id, 'daily');
			return true;
		}

		if($schedule->repeat_frequency() === 'weekly'){
			$timestamps = $this->weekly_timestamps($schedule->start_date(), $schedule->weekly_times());
			$this->schedule_cron($timestamps, $schedule_id, 'weekly');
			return true;
		}

		$timestamps = $this->monthly_timestamps($schedule->start_date(), $schedule->monthly_times());
		$this->schedule_cron($timestamps, $schedule_id, 'monthly');

		return true;
	}

	public function update_schedule(): bool {

	}

	public function delete_schedule(): bool {

	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function next_schedule(int $id ): int|false {
		/**
		 * @var Schedule $schedule
		 */
		$schedule = $this->schedule_repository->get($id);

		if($schedule->repeat_frequency() === 'none'){
			return wp_next_scheduled('nevamiss_schedule_single_events', array($schedule->id()));
		}
		return wp_next_scheduled('nevamiss_multi_time_events', array($schedule->id()));
	}

	private function to_date(array $dates): array
	{
		return array_map(
			function(string $date){
				return Date::create_from_format($date, 'Y-m-d H:s')->timestamp();
			},
			$dates
		);
	}

	private function schedule_one_cron(array $dates, int $schedule_id): void
	{
		foreach( $dates as $date){
			wp_schedule_single_event($date, 'nevamiss_schedule_single_events', [$schedule_id]);
		}
	}

	/**
	 * @throws \Exception
	 */
	private function schedule_cron(array $dates, int $schedule_id, $frequency): void
	{
		foreach ($dates as $date){
			$scheduled = wp_schedule_event(
				$date,
				$frequency,
				'nevamiss_multi_time_events',
				[$schedule_id]
			);
			if(!$scheduled){
				throw new \Exception("Schedule with id: $schedule_id, was unable to schedule");
			}
		}
	}

	private function monthly_timestamps(string $start_date, array $times): array
	{
		$date = Date::create_from_format($start_date);

		$timestamps = [];

		foreach ($times as $time){
			$date->set_day($time['day']);
			$date->set_time($time['hour'], $time['minute']);

			if($date->is_late()){
				$date->next_active_date('+1 month');
			}
			$timestamps[] = $date->timestamp();
		}
		return $timestamps;
	}

	private function weekly_timestamps(string $start_date, array $times): array
	{
		$timestamps = [];

		foreach( $times as $time){
			$date = Date::create_from_format($start_date);
			$date->set_time($time['hour'], $time['minute']);

			for( $day = 0; $day < 7; $day++){

				if($time['day'] !== $date->day()){
					$date->next_date();
					continue;
				}

				if($date->is_late()){
					$date->next_active_date('+7 day');
				}
				$timestamps[] = $date->timestamp();
				$date->next_date();
			}
		}

		return $timestamps;
	}

	private function daily_times(string $start_date, array $daily_times): array
	{

		$timestamps = [];

		foreach ($daily_times as $time){
			$date = Date::create_from_format($start_date);
			$date->set_time($time['hour'], $time['minute']);

			if($date->is_late()){
				$date->next_active_date('+1 day');
			}
			$timestamps[] = $date->timestamp();
		}
		return $timestamps;
	}
}
