<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Service\Settings;

class Settings_Page extends Page
{
    public const TEMPLE_PATH = 'templates/settings';

    public function __construct(
        Settings $settings,
        string $title,
        string $slug,
        string $filename,
        int $priority
    )
    {
        parent::__construct($settings, $title, $slug, $filename, $priority);
    }
    
}