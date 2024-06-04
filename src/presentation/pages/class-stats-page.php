<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Repositories\Posts_Stats_Repository;

class Stats_Page extends Page
{
    public const TEMPLE_PATH = 'templates/stats';

    public function __construct(
        Posts_Stats_Repository $stats,
        string $title,
        string $slug,
        string $filename,
        int $priority
    )
    {
        parent::__construct($stats, $title, $slug, $filename, $priority);
    }

}