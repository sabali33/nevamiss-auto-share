<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Entities\Schedule;

class Schedule_View_Page extends Page
{
    public const TEMPLE_PATH = 'templates/schedule-view';

    public function __construct(
        Schedule $schedule,
        string $title,
        string $slug,
        string $filename,
        int $priority
    )
    {
        parent::__construct($schedule, $title, $slug, $filename, $priority);
    }

}