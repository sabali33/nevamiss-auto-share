<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Delete_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Contracts\Update_Interface;
use Nevamiss\Domain\Entities\Schedule;

class Schedule_Repository implements Create_Interface, Get_One_Interface, Get_All_Interface, Update_Interface, Delete_Interface
{
    use Repository_Common;
    use Create_Trait;
    use Update_Trait;
    private const ALLOW_TABLE_COLUMNS = [
        'id',
        'name',
        'start_date',
        'repeat_frequency',
        'daily_times',
        'weekly_times',
        'monthly_times',
        'query',
        'accounts'
    ];

    public function get_all(array $data = [])
    {
        throw new \Exception("Implement this method");
    }

    /**
     * @throws Not_Found_Exception
     */
    public function get(int $id): Schedule
    {
        $sql = $this->wpdb->prepare("SELECT * FROM {$this->table_name()} WHERE id = '%s'", $id);
        $schedule = $this->wpdb->get_results($sql);
        return $this->factory->new(Schedule::class, $schedule);
    }

    public function delete(int $id)
    {
        throw new \Exception("Implement this method");
    }

    private function table_name(): string
    {
        return "{$this->wpdb->prefix}_nevamiss_schedule";
    }
}