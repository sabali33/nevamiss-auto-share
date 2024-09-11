<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Exception;
use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Application\Post_Query\Query;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Repositories\Schedule_Queue_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;

class Network_Post_Aggregator {

	public function __construct(
		private Schedule_Repository $schedule_repository,
		private Schedule_Queue_Repository $schedule_queue_repository,
		private WP_Cron_Service $cron_service,
		private Query $query
	)
	{
	}

	/**
	 * @throws Not_Found_Exception
	 * @throws Exception
	 */
	public function upcoming_posts(): array
	{
		$schedules = $this->schedule_repository->get_all();
		$aggregate = $this->extract_data($schedules);
		$ordered_data = $this->order_aggregate($aggregate);

		return $this->format_posting_times($ordered_data);
	}

	/**
	 * @throws Not_Found_Exception
	 */
	private function queued_posts(int $schedule_id, int $posts_count ): array
	{
		/**
		 * @var \Nevamiss\Domain\Entities\Schedule_Queue $queue
		 */
		$queue = $this->schedule_queue_repository->get_schedule_queue_by_schedule_id($schedule_id);
		$posts_ids = $queue->all_posts_ids();

		if(count($posts_ids) <= $posts_count ){
			return $this->to_posts($posts_ids);
		}
		$posts_ids = array_slice($posts_ids, 0, $posts_count, true);

		return $this->to_posts($posts_ids);
	}

	private function posting_on(int $schedule_id): array
	{
		return $this->cron_service->schedule_crons($schedule_id);

	}

	private function to_posts(array $posts_ids): array
	{
		return array_map(function($post_id){
			$post = $this->query->post($post_id);
			return [$post->ID, $post->post_title];
		}, $posts_ids);
	}

	/**
	 * @param array $upcoming_data
	 * @return array|string[]
	 * @throws Exception
	 */
	private function format_posting_times(array $upcoming_data): array
	{
		return array_map(/**
		 * @throws Exception
		 */ function(array $data){

			 $posting_times = array_map( function ($post_time) {
				 $date_string = Date::timestamp_to_date($post_time);

				 $date = Date::create_from_format($date_string, 'Y-m-d H:i:s');

				 $time_diff = Date::now()->diff($date);

				 if ($time_diff->d < 1) {
					 $human_time = human_time_diff(Date::now()->timestamp(), $date->timestamp());
					 return sprintf(esc_html__("Posting in %s", 'nevamiss'), $human_time);
				 }
				 if ($time_diff->d === 1) {
					 return sprintf(esc_html__("Posting Tomorrow @ %s", 'nevamiss'), $date->format($date->time_format()));
				 }

				 return $date->format($date->full_wp_date_format());
			 }, $data['posting_times']);

			 $data['posting_times'] = $posting_times;
			 return $data;

		}, $upcoming_data);
	}

	/**
	 * @param array $schedules
	 * @return array
	 * @throws Not_Found_Exception
	 */
	private function extract_data(array $schedules): array
	{
		$aggregate = [];

		/**
		 * @var Schedule $schedule
		 */
		foreach ($schedules as $schedule) {
			$posting_times = $this->posting_on($schedule->id());

			$aggregate[] = [
				'posts' => $this->queued_posts($schedule->id(), count($posting_times)),
				'posting_times' => $posting_times,
				'schedule_name' => $schedule->name(),
				'id' => $schedule->id()
			];

		}
		return $aggregate;
	}

	private function order_aggregate(array $aggregate): array
	{
		usort($aggregate, function(array $first, array $second){
			$first_earliest_posting_time = $first['posting_times'][0];
			$second_earliest_posting_time = $second['posting_times'][0];
			if( $first_earliest_posting_time > $second_earliest_posting_time){
				return 1;
			}
			if($first_earliest_posting_time < $second_earliest_posting_time){
				return -1;
			}
			return 0;
		});
		return $aggregate;
	}
}