<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Entities\Schedule;

class Schedule_View_Page extends Page
{
    public string $title;
    public int $priority;
    public Schedule $schedule;
    public const TEMPLE_PATH = 'templates/schedule-view';

    public function __construct(Schedule $schedule, $title, $slug, $filename, $priority )
    {
        parent::__construct($schedule, $title, $slug, $filename, $priority);
    }

}