<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Task_Repository;
use Nevamiss\Services\Contracts\Task_Runner_Interface;

class Task_Runner implements Task_Runner_Interface {
    public function __construct(
        private Factory                    $factory,
        private Task_Repository            $task_repository,
        private Network_Account_Repository $network_account_repository,
        private array $network_clients
    )
    {
    }

    /**
     * @throws Not_Found_Exception
     */
    public function run(int $task_id): void
    {
        [
            'class_identifier' => $class_name,
            'parameter' => $parameters,
        ] = $this->task_repository->get($task_id);

        $network_account = $this->network_account_repository->get($parameters['network_account_id']);

        $network_client = $this->network_clients[$network_account->network()];

        /**
         * @var Network_Post_Manager $post_manager
         */
        $post_manager = $this->factory->new($class_name, $network_account, $network_client);

        $response = $post_manager->run($parameters['post_id']);

        \do_action('nevamiss_task_completed', ['results' => $response, 'task' => $task_id]);

    }
}