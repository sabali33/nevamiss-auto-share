<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Exception;
use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Delete_All_Interface;
use Nevamiss\Domain\Contracts\Delete_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;


class Posts_Stats_Repository implements Create_Interface, Delete_Interface, Get_All_Interface, Delete_All_Interface
{
    use Repository_Common;
    use Create_Trait;

    private const ALLOWED_TABLE_COLUMNS = [
        'schedule_id',
        'posted_on',
        'posts_ids',
        'cycles_count',
        'remote_posted',
        'status'
    ];

    public function get_all(array $data = [])
    {
        throw new Exception("Implement this method");
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function clear()
    {

    }

    private function table_name(): string
    {
        return "{$this->wpdb->prefix}_nevamiss_stats";
    }
}