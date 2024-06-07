<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Repositories\Schedule_Repository;

class Schedule_View_Page extends Page
{
    public const TEMPLE_PATH = 'templates/schedule-view';
    /**
     * @var true
     */
    private bool $is_sub_page;

    public function __construct(
        Schedule_Repository $schedule_repository,
        string $title,
        string $slug,
        string $filename,
        int $priority
    )
    {
        parent::__construct($schedule_repository, $title, $slug, $filename, $priority);
        $this->is_sub_page = true;
    }

}