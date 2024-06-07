<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Repositories\Posts_Stats_Repository;

class Stats_Page extends Page
{
    public const TEMPLE_PATH = 'templates/stats';
    /**
     * @var true
     */
    private bool $is_sub_page;

    public function __construct(
        Posts_Stats_Repository $stats,
        string $title,
        string $slug,
        string $filename,
        int $priority
    )
    {
        parent::__construct($stats, $title, $slug, $filename, $priority);
        $this->is_sub_page = true;
    }

}