<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Exception;
use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Domain\Repositories\Task_Repository;

class Schedule_Post_Manager {

    public function __construct(
        private Schedule_Repository        $schedule_repository,
        private Network_Account_Repository $account_repository,
        private Factory                    $factory,
        private Task_Repository            $task_repository,
        private Post_Formatter             $formatter,
        private array                      $network_clients,
    )
    {

    }

    /**
     * @param int $schedule_id Schedule Id.
     * @return void
     * @throws Not_Found_Exception
     * @throws Exception
     */
    public function run(int $schedule_id): void
    {
        $schedule = $this->schedule_repository->get($schedule_id);
        $data_set = $schedule->post_data();

        if($schedule->is_heavy()){
            $this->instant_post($data_set);
            return;
        }

        $this->create_tasks($schedule, $data_set);
    }

    /**
     * @throws Not_Found_Exception
     */
    private function instant_post(array $data_set): void
    {
        /**
         * @var array{account_id:int, post_id: int} $item
         */
        foreach ($data_set as $item ){

            $network_account = $this->account_repository->get($item['account_id']);
            $network_client = $this->network_clients[$network_account->network()];
            $data = $this->formatter->format($item['post_id']);

            $network_post_manager = $this->factory->new(
                Network_Post_Manager::class,
                $network_account,
                $network_client
            );

            $network_post_manager->post($data);
        }
    }

    /**
     * @throws Exception
     */
    private function create_tasks(Schedule $schedule, array $data_set): void
    {
        $task_data = $this->set_task_schedule_ids($schedule, $data_set);

        foreach ($task_data as $data ){
            $this->task_repository->create($data);
        }

        do_action('schedule_create_tasks_completed', $schedule->id() );
    }

    /**
     * @param Schedule $schedule
     * @param array $data_set
     * @return void[]
     */
    private function set_task_schedule_ids(Schedule $schedule, array $data_set): array
    {
        return array_map(function ($data) use ($schedule) {

            return [
                'class_identifier' => Schedule_Tasks_Runner::class,
                'parameters' => $data,
                'schedule_id' => $schedule->id(),
            ];
        }, $data_set);
    }
}