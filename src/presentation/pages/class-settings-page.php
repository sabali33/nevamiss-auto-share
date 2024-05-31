<?php

namespace Nevamiss\Presentation\Pages;

class Settings_Page extends Page
{
    public string $title;
    public int $priority;
    public array $settings;
    public const TEMPLE_PATH = 'templates/settings';

    public function __construct($settings, $title, $slug, $filename, $priority )
    {
        parent::__construct($settings, $title, $slug, $filename, $priority);
    }
    
}