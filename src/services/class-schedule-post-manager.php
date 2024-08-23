<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Exception;
use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Domain\Repositories\Task_Repository;
use Nevamiss\Networks\Contracts\Network_Clients_Interface;

class Schedule_Post_Manager {

	public function __construct(
		private Schedule_Repository $schedule_repository,
		private Factory $factory,
		private Task_Repository $task_repository,
		private Network_Post_Provider $schedule_provider,
		private Settings $settings,
	) {
	}

	/**
	 * @param int $schedule_id Schedule Id.
	 * @return void
	 * @throws Not_Found_Exception
	 * @throws Exception
	 */
	public function run( int $schedule_id ): void {

		$instant_sharing = doing_action('admin_post_nevamiss_schedule_share');

		do_action(Logger::SCHEDULE_LOGS, ["Preparing to share"], $schedule_id);

		if ( $this->settings->pause_all_schedules() ) {

			do_action(Logger::SCHEDULE_LOGS, ["Scheduling is paused at Settings", true], $schedule_id);

			if($instant_sharing){
				throw new Exception(__("Scheduling is paused at Settings", 'nevamiss'));
			}
			return;
		}

		try {
			/**
			 * @var Schedule $schedule
			 */
			$schedule = $this->schedule_repository->get( $schedule_id );

			if ( ! $schedule->is_heavy() ) {

				$data_set = $this->schedule_provider->provide_instant_share_data( $schedule );

				do_action(Logger::SCHEDULE_LOGS, ["Starting to post without creating tasks"], $schedule_id);

				$this->instant_post( $data_set, $schedule_id );

				do_action(Logger::SCHEDULE_LOGS, ["Post shared without creating tasks", true], $schedule_id);

				return;
			}

			$data_set = $this->schedule_provider->provide_for_schedule( $schedule );

			$this->create_tasks( $schedule, $data_set );

			do_action(Logger::SCHEDULE_LOGS, ["Successfully shared from tasks", true], $schedule_id);

		}catch (\Throwable $exception){

			do_action(Logger::SCHEDULE_LOGS, [$exception->getMessage(), true ], $schedule_id);

			if($instant_sharing){
				throw new Exception($exception->getMessage());
			}

		}

	}

	/**
	 * @param array[] $data_set
	 * @throws Not_Found_Exception
	 */
	private function instant_post( array $data_set, int $schedule_id ): void {
		/**
		 * @var array{data: string, account: Network_Account, network_client: Network_Clients_Interface} $item
		 */
		foreach ( $data_set as $item ) {

			[
				'account' => $network_account,
				'network_client' => $network_client,
				'data' => $data
			] = $item;

			/**
			 * @var Network_Post_Manager $network_post_manager
			 */
			$network_post_manager = $this->factory->new(
				Network_Post_Manager::class,
				$network_account,
				$network_client
			);

			$remote_id = $network_post_manager->post( $data );

			do_action( 'nevamiss_schedule_network_share', $remote_id, $schedule_id );

			sleep( 1 );
		}
	}

	/**
	 * @throws Exception
	 */
	private function create_tasks( Schedule $schedule, array $data_set ): void {

		do_action(Logger::SCHEDULE_LOGS, ["Starting to create tasks"], $schedule->id());

		foreach ( $data_set as $data ) {
			$this->task_repository->create( $data );
		}

		do_action(Logger::SCHEDULE_LOGS, ["Finished creating tasks"], $schedule->id());

		do_action( 'nevamiss_schedule_create_tasks_completed', $schedule->id() );
	}
}
