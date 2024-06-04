<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Contracts\Update_Interface;

class Schedule_Queue_Repository implements Create_Interface, Get_One_Interface, Update_Interface
{
    use Repository_Common;
    use Create_Trait;
    use Update_Trait;

    private const ALLOWED_TABLE_COLUMNS = [
        'id',
        'schedule_id',
        'shared_posts_ids',
        'all_posts_ids',
    ];

        public function get(int $id)
    {
        throw new \Exception("Implement this method");
    }

    private function table_name(): string
    {
        return "{$this->wpdb->prefix}_nevamiss_schedule_queue";
    }
}