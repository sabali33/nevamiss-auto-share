<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use factory\Factory;
use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Contracts\Update_Interface;
use Nevamiss\Domain\Entities\Task;

class Task_Repository implements Create_Interface, Get_One_Interface, Get_All_Interface, Update_Interface
{

    use RepositoryCommon;

    public function create(mixed $data): bool
    {
        $columns = implode(',', array_keys($data) );
        $values = implode(',', array_values($data) );

        $sql = $this->wpdb->prepare("INSERT INTO {$this->table_name()} ($columns) VALUES ($values)", $data);

        $result = $this->wpdb->query($sql);

        if(!$result){
            return false;
        }
        return true;
    }

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
        $sql = $this->wpdb->prepare("SELECT * FROM {$this->wpdb->prefix}_nevamiss_task WHERE id='%s'", $id);

        $task = $this->wpdb->get_results($sql, ARRAY_A);

        if(!$task){
            throw new Not_Found_Exception("Task with the ID not found");
        }

        return $this->factory->new(Task::class, $task);
    }

    /**
     * @param int $id Task Id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        if(!isset($data['status'])){
            return false;
        }

        $result = $this->wpdb->update(
            $this->table_name(),
            [
                'status' => $data['status']
            ],
            [
                'id' => $id
            ]
        );

        if(!$result){
            return false;
        }
        return true;
    }

    private function table_name(): string
    {
        return "{$this->wpdb->prefix}_nevamiss_task";
    }
}