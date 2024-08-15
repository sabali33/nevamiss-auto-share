<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Exception;
use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Application\Post_Query\Query;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Repositories\Schedule_Queue_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;

class Schedule_Queue {

	private const HOUR_DIVISOR_IN_SECONDS = 60 * 60;
	private const DAY_DIVISOR_IN_SECONDS  = self::HOUR_DIVISOR_IN_SECONDS * 24;
	public function __construct(
		private Schedule_Repository $schedule_repository,
		private Schedule_Queue_Repository $queue_repository,
		private Query $query,
	) {
	}

	/**
	 * @throws Not_Found_Exception
	 * @throws Exception
	 */
	public function create_queue_callback( int $schedule_id ): void {
		/**
		 * @var Schedule $schedule
		 */
		$schedule = $this->schedule_repository->get( $schedule_id );

		$query_args = wp_parse_args(
			array(
				'fields'         => 'ids',
				'posts_per_page' => -1,
			),
			$schedule->query_args()
		);

		$post_ids = $this->query->query( $query_args );

		$id = $this->queue_repository->create(
			array(
				'schedule_id'   => $schedule_id,
				'all_posts_ids' => wp_json_encode( $post_ids ),
			)
		);

		do_action( 'nevamiss_schedule_queue_created', $id );
	}

	/**
	 * @param Schedule $schedule Schedule before form update.
	 * @return void
	 * @throws Not_Found_Exception
	 * @throws Exception
	 */
	public function maybe_update_schedule_queue( Schedule $schedule ): void {

		/**
		 * @var Schedule $updated_schedule
		 */
		$updated_schedule = $this->schedule_repository->get( $schedule->id() );

		$override_sort = false;
		if ( $schedule->query_args()['orderby'] !== $updated_schedule->query_args()['orderby'] ) {
			$override_sort = true;
		}

		if ( $updated_schedule->query_args()['orderby'] === 'none' ) {
			$override_sort = false;
		}
		if ( ! $override_sort ) {
			return;
		}
		$this->update_schedule_queue( $updated_schedule, true );
	}

	/**
	 * @throws Not_Found_Exception
	 * @throws Exception
	 */
	public function update_schedule_queue_callback( int $task_id, array $args ): void {
		[
			'schedule_id' => $schedule_id,
			'post_id' => $post_id,
			'status' => $status
		] = $args;

		if('failed' === $status){
			return;
		}
		$schedule_queue = $this->queue_repository->get_schedule_queue_by_schedule_id( $schedule_id );

		if ( ! $schedule_queue ) {
			return;
		}
		$shared_posts = $schedule_queue->shared_posts_ids();

		if ( in_array( $post_id, $shared_posts ) ) {
			return;
		}

		$all_posts_ids = $schedule_queue->all_posts_ids();
		$cycles        = $schedule_queue->cycles();

		$post_to_remove = array_shift( $all_posts_ids );

		if ( $post_to_remove !== $post_id ) {
			error_log( "A wrong post was shared. Expected $post_to_remove, but got $post_id" );
		}

		$shared_posts         = array( ...$shared_posts, $post_id );
		$sorted_all_posts_ids = array( ...$all_posts_ids, $post_to_remove );
		$has_cycle_ended      = false;

		if ( $shared_posts == $sorted_all_posts_ids ) {
			$has_cycle_ended = true;
			$shared_posts    = null;
			$cycles          = $schedule_queue->cycles() + 1;
		}

		$this->queue_repository->update(
			$schedule_queue->id(),
			array(
				'shared_posts_ids' => $shared_posts ? wp_json_encode( $shared_posts ) : null,
				'all_posts_ids'    => wp_json_encode( $sorted_all_posts_ids ),
				'cycles'           => $cycles,
			),
		);

		if ( $has_cycle_ended ) {

			do_action( 'nevamiss_schedule_cycle_completed', $schedule_id );
		}
	}

	/**
	 * @param Schedule $schedule
	 * @param bool     $override_sort
	 * @return void
	 * @throws Exception
	 */
	private function update_schedule_queue(
		Schedule $schedule,
		bool $override_sort = false
	): void {
		$schedule_queue = $this->queue_repository->get_schedule_queue_by_schedule_id( $schedule->id() );

		if ( ! $schedule_queue ) {
			return;
		}
		$order = 'DESC';

		switch ( $schedule->query_args()['orderby'] ) {
			case 'newest':
				$orderby = 'date';
				break;
			case 'oldest':
				$orderby = 'date';
				$order   = 'ASC';
				break;
			case 'queue_order':
				$orderby = 'none';
				break;
			case 'modified_date':
				$orderby = 'modified_date';

			default:
				$orderby = 'ID';
		}
		$query_args = wp_parse_args(
			array(
				'fields'         => 'ids',
				'posts_per_page' => -1,
				'orderby'        => $orderby,
				'order'          => $order,
			),
			$schedule->query_args()
		);

		$post_ids = $this->query->query( $query_args );

		$post_ids_in_queue = $schedule_queue->all_posts_ids();
		$shared_posts      = $schedule_queue->shared_posts_ids();

		$unshared_posts = array_filter(
			$post_ids,
			function ( $post_id ) use ( $shared_posts ) {
				return ! in_array( $post_id, $shared_posts );
			}
		);

		if ( $override_sort ) {
			$updated_posts = array( ...$unshared_posts, ...$shared_posts );

			$this->queue_repository->update(
				$schedule_queue->id(),
				array(
					'all_posts_ids' => wp_json_encode( $updated_posts ),
				)
			);
			return;
		}

		$new_posts_ids = array_filter(
			$post_ids,
			function ( $post_id ) use ( $post_ids_in_queue ) {
				return ! in_array( $post_id, $post_ids_in_queue );
			}
		);

		$updated_posts = array( ...$post_ids_in_queue, ...$new_posts_ids );

		$this->queue_repository->update(
			$schedule_queue->id(),
			array(
				'all_posts_ids' => wp_json_encode( $updated_posts ),
			)
		);
	}

	/**
	 * @throws Not_Found_Exception
	 * @throws Exception
	 */
	public function schedule_posts( Schedule $schedule ): array {
		/**
		 * @var \Nevamiss\Domain\Entities\Schedule_Queue $queue
		 */
		$queue = $this->queue_repository->get_schedule_queue_by_schedule_id( $schedule->id() );

		if ( ! $queue ) {
			throw new Exception( "No queues found for this schedule({$schedule->name()})" );
		}

		$posts_count = (int) $schedule->query_args()['posts_per_page'];
		$post_ids    = array_slice( $queue->all_posts_ids(), 0, $posts_count );

		return $this->posts_by_ids( $post_ids );
	}

	public function posts_by_ids( array $post_ids ): array {
		$posts = $this->query->query( array( 'post__in' => $post_ids ) );

		return array_map(
			function ( \WP_Post $post ) {
				return array(
					'post_title' => $post->post_title,
					'link'       => get_permalink( $post->ID ),
				);
			},
			$posts
		);
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function estimate_schedule_cycle_completion( Schedule $schedule ): array {
		/**
		 * @var \Nevamiss\Domain\Entities\Schedule_Queue|false $queue
		 */
		$queue = $this->queue_repository->get_schedule_queue_by_schedule_id( $schedule->id() );

		if ( ! $queue ) {
			return array(
				'month'  => 0,
				'day'    => 0,
				'hour'   => 0,
				'minute' => 0,
			);
		}

		$all_posts    = $queue->all_posts_ids();
		$shared_posts = $queue->shared_posts_ids();

		$remaining_posts_count = count( $all_posts ) - count( $shared_posts );

		return $this->schedule_estimated_completion_time( $schedule, $remaining_posts_count );
	}

	private function schedule_estimated_completion_time( Schedule $schedule, int $posts_count ): array {
		return match ( $schedule->repeat_frequency() ) {
			'monthly' => $this->estimate_monthly_schedule( $schedule, $posts_count ),
			'weekly' => $this->estimate_weekly_schedule( $schedule, $posts_count ),
			'daily' => $this->estimate_daily_schedule( $schedule, $posts_count ),
		};
	}

	private function estimate_monthly_schedule( Schedule $schedule, int $posts_count ): array {
		$time_units = array();

		$date     = Date::create_from_format( $schedule->start_date() );
		$per_page = (int) $schedule->query_args()['posts_per_page'];

		// Ensure that date remains current
		$this->ensure_that_date_remains_current( $date );

		$shared_per_cycle = count( $schedule->monthly_times() );

		$number_of_posting_per_monthly = $shared_per_cycle * $per_page;
		$months                        = floor( $posts_count / $number_of_posting_per_monthly );

		if ( $posts_count <= $per_page || ( $posts_count <= $number_of_posting_per_monthly ) ) {
			$end_date = $this->last_cycle_date( $date, $posts_count, $schedule );
			return $this->hour_minute( $date, $end_date );
		}
		$remaining_posts = $posts_count % $number_of_posting_per_monthly;

		$date->modify( "+$months month" );

		$time_units['month'] = $months;

		if ( $remaining_posts === 0 ) {

			return array_merge( $time_units, $this->no_lower_time_units( $time_units ) );
		}

		$end_date = $this->last_cycle_date( $date, $remaining_posts, $schedule );

		return array_merge( $time_units, $this->hour_minute( $date, $end_date ) );
	}

	/**
	 * @param Date $date
	 * @param Date $end_date
	 * @return array
	 */
	private function hour_minute( Date $date, Date $end_date ): array {
		$time_units                 = array();
		$time_difference_in_seconds = $end_date->timestamp() - $date->timestamp();

		$time_difference_in_seconds = $time_difference_in_seconds % ( self::DAY_DIVISOR_IN_SECONDS * 30 );

		$time_units['day'] = floor( $time_difference_in_seconds / self::DAY_DIVISOR_IN_SECONDS );
		$remaining_seconds = $time_difference_in_seconds % self::DAY_DIVISOR_IN_SECONDS;

		$time_units['hour'] = floor( $remaining_seconds / self::HOUR_DIVISOR_IN_SECONDS );
		$remaining_seconds  = $remaining_seconds % self::HOUR_DIVISOR_IN_SECONDS;

		$time_units['minute'] = ceil( $remaining_seconds / 60 );

		$format = "{$end_date->date_format()} @ {$end_date->time_format()}";

		$time_units['finish_date'] = $end_date->format( $format );

		return $time_units;
	}

	/**
	 * @param Date     $date
	 * @param int      $remaining_posts
	 * @param Schedule $schedule
	 * @return Date|false
	 */
	private function last_cycle_date( Date $date, int $remaining_posts, Schedule $schedule ): Date|false {
		$end_date = Date::create_from_format( $date->format( 'Y-m-d' ) );

		$number_of_days_to_post = ceil( $remaining_posts / (int) $schedule->query_args()['posts_per_page'] );

		$method_name = "{$schedule->repeat_frequency()}_times";
		$post_times  = call_user_func( array( $schedule, $method_name ) );

		$last_day = $post_times[ $number_of_days_to_post - 1 ];

		if ( $schedule->repeat_frequency() === 'monthly' ) {
			$this->monthly_last_date_update( $end_date, $last_day );
			return $end_date;
		}
		if ( $schedule->repeat_frequency() === 'daily' ) {
			$this->update_time( $end_date, $last_day );
			return $end_date;
		}

		for ( $day = 1; $day <= 7; $day++ ) {

			if ( $end_date->day() !== $last_day['day'] ) {
				$end_date->modify( '+1 day' );
				continue;
			}

			$this->update_time( $end_date, $last_day );
			return $end_date;
		}
		return false;
	}

	private function estimate_weekly_schedule( Schedule $schedule, int $posts_count ): array {
		$time_units = array();
		$date       = Date::create_from_format( $schedule->start_date() );

		// Ensure that date remains current
		$this->ensure_that_date_remains_current( $date );
		$per_page             = (int) $schedule->query_args()['posts_per_page'];
		$posting_times        = $schedule->weekly_times();
		$sharing_count_a_week = count( $posting_times );

		$number_of_posting_per_week = $sharing_count_a_week * $per_page;

		if ( $posts_count <= $per_page || ( $posts_count <= $number_of_posting_per_week ) ) {
			$end_date = $this->last_cycle_date( $date, $posts_count, $schedule );
			return $this->hour_minute( $date, $end_date );
		}

		$weeks = floor( $posts_count / $number_of_posting_per_week );

		$days_from_week = $weeks * 7;

		$remaining_posts = $posts_count % $number_of_posting_per_week;

		$date->modify( "+$days_from_week day" );

		$month_from_weeks    = floor( $days_from_week / 30 );
		$time_units['month'] = $month_from_weeks;

		if ( $days_from_week && ! $time_units['month'] ) {
			$time_units['day'] = $days_from_week;
		}
		if ( $remaining_posts === 0 ) {
			return $this->exact_end_date( $date, $time_units['day'], $posting_times[0] );
		}

		$end_date = $this->last_cycle_date( $date, $remaining_posts, $schedule );

		return array_merge( $time_units, $this->hour_minute( $date, $end_date ) );
	}

	private function estimate_daily_schedule( Schedule $schedule, int $posts_count ): array {
		$time_units = array();
		$date       = Date::create_from_format( $schedule->start_date() );

		// Ensure that date remains current
		$this->ensure_that_date_remains_current( $date );
		$per_page      = (int) $schedule->query_args()['posts_per_page'];
		$posting_times = $schedule->daily_times();

		$sharing_count_a_day = count( $posting_times );

		$number_of_posting_per_day = $sharing_count_a_day * $per_page;

		if ( $posts_count <= $per_page || ( $posts_count <= $number_of_posting_per_day ) ) {
			$end_date = $this->last_cycle_date( $date, $posts_count, $schedule );
			return $this->hour_minute( $date, $end_date );
		}

		$days_required_to_finish_posting = floor( $posts_count / $number_of_posting_per_day );

		if ( $days_required_to_finish_posting > 29 ) {
			$time_units['months'] = $days_required_to_finish_posting;
		} else {
			$time_units['day'] = $days_required_to_finish_posting;
		}

		$remaining_posts = $posts_count % $number_of_posting_per_day;

		if ( $remaining_posts === 0 ) {
			return $this->exact_end_date( $date, $time_units['day'], $posting_times[0] );
		}

		$end_date = $this->last_cycle_date( $date, $remaining_posts, $schedule );

		return array_merge( $time_units, $this->hour_minute( $date, $end_date ) );
	}

	/**
	 * @param Date $date
	 * @return void
	 */
	private function ensure_that_date_remains_current( Date $date ): void {
		if ( ! $date->is_late() ) {
			return;
		}

		$modifiers = $this->modifiers( $date );

		foreach ( $modifiers as $modifier ) {
			$date->next_active_date( $modifier );
		}
	}

	/**
	 * @param Date $date
	 * @return array
	 */
	private function modifiers( Date $date ): array {
		$time_difference = Date::now()->timestamp() - $date->timestamp();

		$difference_in_months = floor( $time_difference / ( self::DAY_DIVISOR_IN_SECONDS * 30 ) );
		$remaining_difference = $time_difference % ( self::DAY_DIVISOR_IN_SECONDS * 30 );

		$difference_in_days   = floor( $remaining_difference / self::DAY_DIVISOR_IN_SECONDS );
		$remaining_difference = $remaining_difference % self::DAY_DIVISOR_IN_SECONDS;

		$difference_in_hours  = $remaining_difference / self::HOUR_DIVISOR_IN_SECONDS;
		$remaining_difference = $remaining_difference % self::HOUR_DIVISOR_IN_SECONDS;

		$modifiers = array();

		if ( $difference_in_months > 0 ) {
			$modifiers[] = "+$difference_in_months month";
		}
		if ( $difference_in_days ) {
			$modifiers[] = "+$difference_in_days day";
		}
		if ( $difference_in_hours ) {
			$modifiers[] = "+$difference_in_hours day";
		}
		$difference_in_minutes = floor( $remaining_difference / 60 );
		$modifiers[]           = "+$difference_in_minutes minute";

		return $modifiers;
	}

	/**
	 * @param array $time_units
	 * @return array
	 */
	private function no_lower_time_units( array $time_units ): array {
		$time_units['day']    = $time_units['day'] ?? 0;
		$time_units['hour']   = 0;
		$time_units['minute'] = 0;
		return $time_units;
	}

	/**
	 * @param Date  $end_date
	 * @param mixed $last_day
	 * @return void
	 */
	private function monthly_last_date_update( Date &$end_date, array $last_day ): void {
		$end_date->modify( "+{$last_day['day']} day" );
		$end_date->set_day( $last_day['day'] );
		$end_date->set_day( $last_day['day'] );

		$this->update_time( $end_date, $last_day );
	}

	private function update_time( Date &$end_date, array $last_day ): void {
		$end_date->set_time( $last_day['hour'], $last_day['minute'] );
	}

	/**
	 * @param Date     $date
	 * @param $day
	 * @param $last_day
	 * @return array
	 */
	private function exact_end_date( Date $date, $day, $last_day ): array {
		$end_date = Date::create_from_format( $date->format( 'Y-m-d H:i' ), 'Y-m-d H:i' );
		$end_date->modify( "+{$day} day" );
		$this->update_time( $end_date, $last_day );

		return $this->hour_minute( $date, $end_date );
	}
}
