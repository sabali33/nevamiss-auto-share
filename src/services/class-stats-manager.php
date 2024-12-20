<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\Repositories\Posts_Stats_Repository;

class Stats_Manager {

	public function __construct( private Posts_Stats_Repository $stats_repository ) {
	}

	/**
	 * @throws \Exception
	 */
	public function record_stats_callback( int $task_id, array $args ): void {
		[
			'schedule_id' => $schedule_id,
			'post_id' => $post_id,
			'status' => $status
		] = $args;

		if ( 'error' === $status ) {
			return;
		}

		$this->stats_repository->create(
			array(
				'post_id'        => $post_id,
				'remote_post_id' => $args['remote_post_id'],
				'remote_posted'  => 0,
				'schedule_id'    => $schedule_id,
			)
		);
	}
}
