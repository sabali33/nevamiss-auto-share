<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Services\Settings;

class Settings_Page extends Page
{
    public const TEMPLE_PATH = 'templates/settings';
    /**
     * @var true
     */
    private bool $is_sub_page;

    public function __construct(
        Settings $settings,
        string $title,
        string $slug,
        string $filename,
        int $priority
    )
    {
        parent::__construct($settings, $title, $slug, $filename, $priority);
        $this->is_sub_page = true;
    }
    
}