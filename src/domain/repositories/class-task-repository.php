<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Contracts\Update_Interface;
use Nevamiss\Domain\Entities\Task;
use Nevamiss\Factory\Factory;

class Task_Repository implements Create_Interface, Get_One_Interface, Get_All_Interface, Update_Interface
{

    public function __construct(private readonly Factory $factory, private \wpdb $wpdb)
    {
    }

    public function create(mixed $data)
    {
        throw new \Exception("Implement this method");
    }

    public function get_all(array $data = [])
    {
        throw new \Exception("Implement this method");
    }

    /**
     * @throws Not_Found_Exception
     */
    public function get(int $id)
    {
        $sql = $this->wpdb->prepare("SELECT * FROM {$this->wpdb->prefix}_nevamiss_task WHERE id='%s'", $id);

        $task = $this->wpdb->get_results($sql, ARRAY_A);

        if(!$task){
            throw new Not_Found_Exception("Task with the ID not found");
        }

        return $this->factory->new(Task::class, $task);
    }

    public function update(array $data)
    {
        throw new \Exception("Implement this method");
    }
}