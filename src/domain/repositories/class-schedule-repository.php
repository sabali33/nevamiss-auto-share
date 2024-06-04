<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use factory\Factory;
use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Delete_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Contracts\Update_Interface;
use Nevamiss\Domain\Entities\Schedule;

class Schedule_Repository implements Create_Interface, Get_One_Interface, Get_All_Interface, Update_Interface, Delete_Interface
{
    use RepositoryCommon;

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
    public function get(int $id): Schedule
    {
        $sql = $this->wpdb->prepare("");
        $schedule = $this->wpdb->get_results($sql);
        return $this->factory->new(Schedule::class, $schedule);
    }

    public function update(int $id, mixed $data)
    {
        throw new \Exception("Implement this method");
    }

    public function delete(int $id)
    {
        throw new \Exception("Implement this method");
    }
}