<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Task_Repository;
use Nevamiss\Services\Contracts\Task_Runner_Interface;

class Task_Runner implements Task_Runner_Interface {
	public function __construct(
		private Factory $factory,
		private Task_Repository $task_repository,
		private Network_Post_Provider $network_post_provider
	) {
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function run( int $task_id ): void {
		[
			'class_identifier' => $class_name,
			'parameter' => $parameters,
		] = $this->task_repository->get( $task_id );

		[
			'account' => $network_account,
			'network_client' => $network_client
		] = $this->network_post_provider->provide_network( $parameters['network_account_id'] );

		$data = $this->network_post_provider->format_post( $parameters['post_id'] );

		/**
		 * @var Network_Post_Manager $post_manager
		 */
		$post_manager = $this->factory->new( $class_name, $network_account, $network_client );

		$response = $post_manager->post( $data );

		do_action(
			'nevamiss_task_completed',
			array(
				'results' => $response,
				'task'    => $task_id,
			)
		);
	}
}
