<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Contracts\Update_Interface;

class Schedule_Queue_Repository implements Create_Interface, Get_One_Interface, Update_Interface
{
    use RepositoryCommon;

    public function create(mixed $data)
    {
        throw new \Exception("Implement this method");
    }

        public function get(int $id)
    {
        throw new \Exception("Implement this method");
    }

    public function update(int $id, mixed $data)
    {
        throw new \Exception("Implement this method");
    }
}