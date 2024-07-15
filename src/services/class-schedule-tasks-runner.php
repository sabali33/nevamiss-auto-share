<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Exception;
use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Entities\Task;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Task_Repository;
use Nevamiss\Services\Contracts\Task_Runner_Interface;

class Schedule_Tasks_Runner implements Task_Runner_Interface {

	public function __construct(
		private Task_Repository $task_repository,
		private Factory $factory,
		private Network_Post_Provider $schedule_provider
	) {
	}

	/**
	 * @param int $schedule_id
	 * @throws Not_Found_Exception
	 */
	public function run( int $schedule_id ): void {
		do_action( 'schedule_task_begins', $schedule_id );

		$active_task = $this->task_repository->get_all(
			array(
				'where' => array(
					'schedule_id' => $schedule_id,
					'status'      => 'pending',
				),
			)
		);

		if ( ! $active_task ) {
			return;
		}

		/**
		 * @var Task $active_task
		 */
		[$active_task] = $active_task;
		$class_name    = $active_task->class_identifier();
		$parameters    = $active_task->parameters();

		[
			'account' => $network_account,
			'network_client' => $network_client
		] = $this->schedule_provider->provide_network( $parameters['account_id'] );

		$data = $this->schedule_provider->format_post( $parameters['post_id'] );

		/**
		 * @var Network_Post_Manager $post_manager
		 */
		$post_manager = $this->factory->new( $class_name, $network_account, $network_client );

		$remote_post_id = $post_manager->post( $data );

		do_action(
			'nevamiss_schedule_task_complete',
			$active_task[0]['id'],
			array(
				'schedule_id'    => $schedule_id,
				'post_id'        => $parameters['post_id'],
				'remote_post_id' => $remote_post_id,
			)
		);

		sleep( 2 );

		$this->run( $schedule_id );
	}

	/**
	 * A callback function that runs where an individual task is completed
	 *
	 * @throws Exception
	 */
	public function update_task( int $task_id ): void {
		$this->task_repository->update( $task_id, array( 'status' => 'succeeded' ) );
	}
}
