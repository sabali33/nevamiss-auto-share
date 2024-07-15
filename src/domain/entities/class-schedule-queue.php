<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Entities;

class Schedule_Queue {

	private int $id;
	private int $schedule_id;
	private ?array $shared_posts_ids;
	private ?array $all_posts_ids;
	private int $cycles;

	public function __construct( array $queue_data ) {
		$this->id          = (int) $queue_data['id'];
		$this->schedule_id = (int) $queue_data['schedule_id'];
		$this->cycles      = (int) $queue_data['cycles'];

		$this->shared_posts_ids = $queue_data['shared_posts_ids'] ? $this->to_array( $queue_data['shared_posts_ids'] ) : array();
		$this->all_posts_ids    = $this->to_array( $queue_data['all_posts_ids'] );
	}

	public function schedule_id(): int {
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

	private function to_array( string $shared_posts_ids ): array {
		$decoded_post_ids = json_decode( $shared_posts_ids, true );

		if ( ! $decoded_post_ids ) {
			return array();
		}
		return array_map(
			function ( $post_id ) {
				return intval( $post_id );
			},
			$decoded_post_ids
		);
	}

	public function cycles(): int
	{
		return $this->cycles;
	}
}
