<?php

namespace Nevamiss\Domain\Entities;

class Schedule_Queue {

	private int $id;
	private int $schedule_id;
	private ?array $shared_posts_ids;
	private ?array $all_posts_ids;

	public function __construct(array $queue_data ) {
		$this->id = $queue_data['id'];
		$this->schedule_id = $queue_data['schedule_id'];

		$this->shared_posts_ids = $queue_data['shared_posts_ids'] ? $this->to_array($queue_data['shared_posts_ids']): [];
		$this->all_posts_ids = $this->to_array($queue_data['all_posts_ids']);
	}

	public function schedule_id(): string {
		return $this->schedule_id;
	}

	public function id(): int {
		return $this->id;
	}
	public function shared_posts_ids(): array {
		return $this->shared_posts_ids;
	}
	public function all_posts_ids(): array {
		return $this->all_posts_ids;
	}

	private function to_array(string $shared_posts_ids): array
	{
		return array_map(function($post_id){
			return intval($post_id);
		}, json_decode($shared_posts_ids, true));
	}
}
