<?php

namespace Nevamiss\Presentation\Pages;

use Saas\Inc\Domain\Repository\Schedule_Repository_Interface;


class Schedules_Page extends Page
{
    public string $title;

    public int $priority;

    public Schedule_Repository_Interface $schedule;

    public const TEMPLE_PATH = 'templates/schedules';

    public function __construct(
        Schedule_Repository_Interface $schedule, 
        string $title, 
        string $slug, 
        string $filename, 
        int $priority 
    ){
        parent::__construct($schedule, $title, $slug, $filename, $priority);
    }

}