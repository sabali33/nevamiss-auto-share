<?php

namespace Nevamiss\Presentation\Pages;

class Stats_Page extends Page
{
    public string $title;
    public int $priority;
    public array $stats;
    public const TEMPLE_PATH = 'templates/stats';

    public function __construct($stats, $title, $slug, $filename, $priority )
    {
        parent::__construct($stats, $title, $slug, $filename, $priority);
    }

}