<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Entities\Schedule;


class Schedules_Page extends Page
{
    public string $title;

    public int $priority;

    public Schedule $schedule;

    public const TEMPLE_PATH = 'templates/schedules';

    public function __construct(
        Schedule $schedule,
        string $title, 
        string $slug, 
        string $filename, 
        int $priority 
    ){
        parent::__construct($schedule, $title, $slug, $filename, $priority);
    }

}