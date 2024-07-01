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
	public function __construct(
		private Schedule_Repository $schedule_repository,
		private Schedule_Queue_Repository $queue_repository,
		private Query $query
	)
	{
	}

	/**
	 * @throws Not_Found_Exception
	 * @throws Exception
	 */
	public function create_queue_callback(int $schedule_id): void
	{
		/**
		 * @var Schedule $schedule
		 */
		$schedule = $this->schedule_repository->get($schedule_id);

		$query_args = wp_parse_args(['fields' => 'ids', 'posts_per_page' => -1], $schedule->query_args());

		$post_ids = $this->query->query($query_args);

		$id = $this->queue_repository->create([
			'schedule_id' => $schedule_id,
			'all_posts_ids' => json_encode($post_ids),
		]);

		do_action('nevamiss_schedule_queue_created', $id);
	}

	/**
	 * @param Schedule $schedule Schedule before form update.
	 * @return void
	 * @throws Not_Found_Exception
	 * @throws Exception
	 */
	public function maybe_update_schedule_queue(Schedule $schedule): void
	{

		/**
		 * @var Schedule $updated_schedule
		 */
		$updated_schedule = $this->schedule_repository->get($schedule->id());

		$override_sort = false;
		if($schedule->query_args()['orderby'] !== $updated_schedule->query_args()['orderby']){
			$override_sort = true;
		}

		if($updated_schedule->query_args()['orderby'] === 'none'){
			$override_sort = false;
		}
		if( !$override_sort ){
			return;
		}
		$this->update_schedule_queue($updated_schedule, true);
	}

	/**
	 * @throws Not_Found_Exception
	 * @throws Exception
	 */
	public function update_schedule_queue_callback(int $task_id, $args): void
	{
		[
			'schedule_id' => $schedule_id,
			'post_id' => $post_id,
		] = $args;

		$schedule_queue = $this->queue_repository->get_schedule_queue_by_schedule_id($schedule_id);

		if(!$schedule_queue){
			return;
		}

		$all_posts_ids = $schedule_queue->all_posts_ids();
		$post_to_remove = array_pop($all_posts_ids);

		if($post_to_remove !== $post_id){
			error_log("A wrong post was shared. Expected $post_to_remove, but got $post_id");
		}
		$shared_posts = [...$schedule_queue->shared_posts_ids(), $post_id];
		$sorted_all_posts_ids = [...$all_posts_ids, $post_to_remove];

		if($shared_posts == $sorted_all_posts_ids){
			$shared_posts= [];
		}

		$this->queue_repository->update(
			$schedule_queue->id(),
			[
				'shared_posts_ids' => $shared_posts,
				'all_posts_ids' => $sorted_all_posts_ids
			],
		);

	}

	/**
	 * @param Schedule $schedule
	 * @param bool $override_sort
	 * @return void
	 * @throws Exception
	 */
	private function update_schedule_queue(
		Schedule $schedule,
		bool $override_sort=false): void
	{
		$schedule_queue = $this->queue_repository->get_schedule_queue_by_schedule_id($schedule->id());

		if( !$schedule_queue ){
			return;
		}
		$order = 'DESC';

		switch ($schedule->query_args()['orderby']) {
			case 'newest':
				$orderby = 'date';
				break;
			case 'oldest':
				$orderby = 'date';
				$order = 'ASC';
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
			['fields' => 'ids', 'posts_per_page' => -1, 'orderby' => $orderby, 'order' => $order],
			$schedule->query_args()
		);

		$post_ids = $this->query->query($query_args);

		$post_ids_in_queue = $schedule_queue->all_posts_ids();
		$shared_posts = $schedule_queue->shared_posts_ids();

		$unshared_posts = array_filter($post_ids, function ($post_id) use ($shared_posts) {
			return !in_array($post_id, $shared_posts);
		});

		if ($override_sort) {
			$updated_posts = [...$unshared_posts, ...$shared_posts];

			$this->queue_repository->update(
				$schedule_queue->id(),
				[
					'all_posts_ids' => json_encode($updated_posts)
				]
			);
			return;
		}

		$new_posts_ids = array_filter($post_ids, function ($post_id) use ($post_ids_in_queue) {
			return !in_array($post_id, $post_ids_in_queue);
		});

		$updated_posts = [...$post_ids_in_queue, ...$new_posts_ids];

		$this->queue_repository->update(
			$schedule_queue->id(),
			[
				'all_posts_ids' => json_encode($updated_posts)
			]
		);
	}
}