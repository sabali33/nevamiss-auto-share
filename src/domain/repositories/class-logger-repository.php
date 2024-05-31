<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Delete_All_Interface;
use Nevamiss\Domain\Contracts\Delete_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;


class Logger_Repository implements Create_Interface, Delete_Interface, Get_All_Interface, Delete_All_Interface
{

    public function create(mixed $data)
    {
        throw new \Exception("Implement this method");
    }

    public function get_all(array $data = [])
    {
        throw new \Exception("Implement this method");
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function clear()
    {
        
    }
}