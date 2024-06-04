<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Repositories\Schedule_Repository;


class Schedules_Page extends Page
{
    public const TEMPLE_PATH = 'templates/schedules';

    public function __construct(
        Schedule_Repository $schedule_repository,
        string $title, 
        string $slug, 
        string $filename, 
        int $priority 
    ){
        parent::__construct($schedule_repository, $title, $slug, $filename, $priority);
    }

}