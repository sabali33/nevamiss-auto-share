<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Contracts\Update_Interface;

class Task_Repository implements Create_Interface, Get_One_Interface, Get_All_Interface, Update_Interface
{

    public function create(mixed $data)
    {
        throw new \Exception("Implement this method");
    }

    public function get_all(array $data = [])
    {
        throw new \Exception("Implement this method");
    }

    public function get(int $id)
    {
        throw new \Exception("Implement this method");
    }

    public function update(array $data)
    {
        throw new \Exception("Implement this method");
    }
}