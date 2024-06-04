<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Task_Repository;

class Schedule_Tasks_Runner {

    public function __construct(
        private Task_Repository            $task_repository,
        private Factory                    $factory,
        private Network_Post_Provider $schedule_provider
    )
    {
    }

    /**
     * @throws Exception
     */
    public function run(int $schedule_id): void
    {
        do_action('schedule_task_begins', $schedule_id);

        $active_task = $this->task_repository->get_all(['schedule_id' => $schedule_id]);

        if(!$active_task){
            return;
        }
        [
            'class_identifier' => $class_name,
            'parameter' => $parameters,
        ] = $active_task[0];

        [
            'account' => $network_account,
            'network_client' => $network_client
        ] = $this->schedule_provider->provide_network($parameters['network_account_id']);

        /**
         * @var Network_Post_Manager $post_manager
         */
        $post_manager = $this->factory->new($class_name, $network_account, $network_client);

        $post_manager->run($parameters['post_id']);

        do_action('schedule_task_complete', $active_task[0]['id']);

        sleep(2);

        $this->run($schedule_id);
    }

    /**
     * A callback function that runs where an individual task is completed
     *
     * @throws Exception
     */
    public function update_task(int $task_id): void
    {
        $this->task_repository->update($task_id, ['status' => 1]);
    }
}