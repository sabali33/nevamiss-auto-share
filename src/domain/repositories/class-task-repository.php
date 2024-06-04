<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Contracts\Update_Interface;
use Nevamiss\Domain\Entities\Task;

class Task_Repository implements Create_Interface, Get_One_Interface, Get_All_Interface, Update_Interface
{

    use Repository_Common;
    use Create_Trait;
    use Update_Trait;

    private  const ALLOWED_TABLE_COLUMNS = [
        'id',
        'class_identifier',
        'parameters',
        'schedule_id',
        'status'
    ];

    public function get_all(array $data = ['status' => 1, 'count' => 1]): array|null
    {
        $sql = $this->wpdb->prepare("");
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * @throws Not_Found_Exception
     */
    public function get(int $id)
    {
        $sql = $this->wpdb->prepare("SELECT * FROM {$this->table_name()} WHERE id='%s'", $id);

        $task = $this->wpdb->get_results($sql, ARRAY_A);

        if(!$task){
            throw new Not_Found_Exception("Task with the ID not found");
        }

        return $this->factory->new(Task::class, $task);
    }

    private function table_name(): string
    {
        return "{$this->wpdb->prefix}_nevamiss_task";
    }
}