<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Contracts\Update_Interface;
use Nevamiss\Domain\Entities\Task;

class Task_Repository implements Create_Interface, Get_One_Interface, Get_All_Interface, Update_Interface
{

    use Repository_Common_Trait;
    use Create_Trait;
    use Update_Trait;
    use Get_One_Trait;
    use Get_All_Trait;

    private const ENTITY_NAME = 'Task';
    private const ENTITY_CLASS = Task::class;
    private  const ALLOWED_TABLE_COLUMNS = [
        'id',
        'class_identifier',
        'parameters',
        'schedule_id',
        'status'
    ];

    private function table_name(): string
    {
        return "{$this->wpdb->prefix}_nevamiss_tasks";
    }
}