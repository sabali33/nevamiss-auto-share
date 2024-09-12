<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Services\Contracts\Cron_Interface;

class WP_Cron_Service implements Cron_Interface {
	const RECURRING_EVENT_HOOK_NAME       = 'nevamiss_multi_time_events';
	const NEVAMISS_SCHEDULE_SINGLE_EVENTS = 'nevamiss_schedule_single_events';

	public function __construct( private Schedule_Repository $schedule_repository ) {
	}

	public function add_cron( array $schedules ): array {
		$schedules['monthly'] = array(
			'interval' => ( 60 * 60 * 24 * 7 ),
			'display'  => esc_html__( 'Monthly', 'nevamiss' ),
		);
		return $schedules;
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function create_cron( int $schedule_id ): bool {
		$schedule = $this->schedule_repository->get( $schedule_id );
		return $this->create_schedule( $schedule );
	}
	/**
	 * @param Schedule $schedule
	 * @return bool
	 * @throws Not_Found_Exception|\Exception
	 */
	private function create_schedule( Schedule $schedule ): bool {

		$schedule_id = $schedule->id();

		if ( $schedule->one_time_schedule() ) {
			$dates = $this->to_date( $schedule->one_time_schedule() );
			$this->schedule_one_cron( $dates, $schedule_id );
			return true;
		}

		if ( $schedule->repeat_frequency() === 'daily' ) {
			$timestamps = $this->daily_times( $schedule->start_date(), $schedule->daily_times() );
			$this->schedule_cron( $timestamps, $schedule_id, 'daily' );
			return true;
		}

		if ( $schedule->repeat_frequency() === 'weekly' ) {
			$timestamps = $this->weekly_timestamps( $schedule->start_date(), $schedule->weekly_times() );
			$this->schedule_cron( $timestamps, $schedule_id, 'weekly' );
			return true;
		}

		$timestamps = $this->monthly_timestamps( $schedule->start_date(), $schedule->monthly_times() );
		$this->schedule_cron( $timestamps, $schedule_id, 'monthly' );

		return true;
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function maybe_reschedule_cron( Schedule $schedule ): void {
		/**
		 * @var Schedule $new_schedule
		 */
		$new_schedule = $this->schedule_repository->get( $schedule->id() );

		$can_reschedule = match ( true ) {
			$new_schedule->repeat_frequency() !== $schedule->repeat_frequency(),
			$new_schedule->daily_times() != $schedule->daily_times(),
			$new_schedule->monthly_times() != $schedule->monthly_times(),
			$new_schedule->weekly_times() != $schedule->weekly_times(),
			$new_schedule->start_date() !== $schedule->start_date(),
			$new_schedule->one_time_schedule() != $schedule->one_time_schedule() => true,
			default => false
		};

		if ( $can_reschedule ) {

			$this->reschedule_cron( $new_schedule );
		}
	}

	public function update_schedule(): bool {
	}

	public function unschedule( int $schedule_id ): int {
		/**
		 * @var Schedule $schedule
		 */
		$schedule = $this->schedule_repository->get($schedule_id);

		if( $schedule->repeat_frequency() === 'none') {

			return wp_clear_scheduled_hook(self::NEVAMISS_SCHEDULE_SINGLE_EVENTS, array( $schedule_id));
		}

		return wp_clear_scheduled_hook( self::RECURRING_EVENT_HOOK_NAME, array( $schedule_id ) );
	}

	public function schedule_crons( int $schedule_id ): array {
		$key = md5( serialize( array( $schedule_id ) ) );

		$crons = array_filter(
			get_option( 'cron' ),
			function ( $cron ) use ( $key ) {
				return isset( $cron[ self::RECURRING_EVENT_HOOK_NAME ][ $key ] ) ||
					isset( $cron[ self::NEVAMISS_SCHEDULE_SINGLE_EVENTS ][$key]);
			}
		);

		return array_keys( $crons );
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function next_schedule( int $id ): int|false {
		/**
		 * @var Schedule $schedule
		 */
		$schedule = $this->schedule_repository->get( $id );

		if ( $schedule->repeat_frequency() === 'none' ) {
			return wp_next_scheduled( self::NEVAMISS_SCHEDULE_SINGLE_EVENTS, array( $schedule->id() ) );
		}
		return wp_next_scheduled( self::RECURRING_EVENT_HOOK_NAME, array( $schedule->id() ) );
	}

	private function to_date( array $dates ): array {
		return array_map(
			function ( string $date ) {
				return Date::create_from_format( $date, 'Y-m-d H:s' )->timestamp();
			},
			$dates
		);
	}

	/**
	 * @throws \Exception
	 */
	private function schedule_one_cron( array $dates, int $schedule_id ): void {
		foreach ( $dates as $date ) {
			$scheduled = wp_schedule_single_event( $date, self::NEVAMISS_SCHEDULE_SINGLE_EVENTS, array( $schedule_id ) );
			if ( ! $scheduled ) {
				throw new \Exception( esc_html( "Schedule with id: $schedule_id, was unable to schedule" ) );
			}
		}
	}

	/**
	 * @throws \Exception
	 */
	private function schedule_cron( array $dates, int $schedule_id, $frequency ): void {

		foreach ( $dates as $date ) {
			$scheduled = wp_schedule_event(
				$date,
				$frequency,
				self::RECURRING_EVENT_HOOK_NAME,
				array( $schedule_id )
			);
			if ( ! $scheduled ) {
				throw new \Exception( esc_html( "Schedule with id: $schedule_id, was unable to schedule" ) );
			}
		}
	}

	private function monthly_timestamps( string $start_date, array $times ): array {

		$timestamps = array();

		foreach ( $times as $time ) {
			$date = Date::create_from_format( $start_date );
			$date->set_day( $time['day'] );
			$date->set_time( $time['hour'], $time['minute'] );

			if ( $date->is_late() ) {
				$date->next_active_date( '+1 month' );
			}
			$timestamps[] = $date->timestamp();
		}
		return $timestamps;
	}

	private function weekly_timestamps( string $start_date, array $times ): array {
		$timestamps = array();

		foreach ( $times as $time ) {
			$date = Date::create_from_format( $start_date );
			$date->set_time( $time['hour'], $time['minute'] );

			for ( $day = 0; $day < 7; $day++ ) {

				if ( $time['day'] !== $date->day() ) {
					$date->next_day();
					continue;
				}

				if ( $date->is_late() ) {
					$date->next_active_date( '+7 day' );
				}
				$timestamps[] = $date->timestamp();
				$date->next_day();
			}
		}

		return $timestamps;
	}

	private function daily_times( string $start_date, array $daily_times ): array {

		$timestamps = array();

		foreach ( $daily_times as $time ) {
			$date = Date::create_from_format( $start_date );
			$date->set_time( $time['hour'], $time['minute'] );

			if ( $date->is_late() ) {
				$date->next_active_date( '+1 day' );
			}
			$timestamps[] = $date->timestamp();
		}
		return $timestamps;
	}

	/**
	 * @param Schedule $new_schedule
	 * @return bool
	 * @throws Not_Found_Exception
	 */
	private function reschedule_cron( Schedule $new_schedule ): bool {
		$this->unschedule( $new_schedule->id() );
		return $this->create_schedule( $new_schedule );
	}

	public function unschedule_all(): void
	{
		/**
		 * @var Schedule[] $schedules
		 */
		$schedules = $this->schedule_repository->get_all();
		foreach ($schedules as $schedule){
			$this->unschedule($schedule->id());
		}
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function schedule_all(): void
	{
		/**
		 * @var Schedule[] $schedules
		 */
		$schedules = $this->schedule_repository->get_all();

		foreach ($schedules as $schedule){
			try{
				$this->create_schedule($schedule);
			}catch (\Throwable $throwable){
				do_action(Logger::GENERAL_LOGS, $throwable->getMessage());
			}

		}
	}
}
