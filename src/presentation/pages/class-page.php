<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Presentation\Contracts\Renderable;

abstract class Page implements Renderable
{
    const TEMPLE_PATH = '';
    protected string $title;
    protected int $priority;
    protected array $data;
    protected string $slug;
    protected string $filename;
    private bool $is_sub_page = false;

    public function __construct(
        mixed $data,
        string $title,
        string $slug,
        string $filename,
        string $priority
    )
    {
        $this->title = $title;
        $this->priority = $priority;
        $this->slug = $slug;
        $this->$filename = $filename;
        $this->data = $data;
    }

    final public function render(): bool|string
    {
        ob_start();

        include NEVAMISS_PATH . '/resources/' . self::TEMPLE_PATH .'.php';

        return ob_get_clean();
    }

    final public function register(): void
    {
        if(!$this->is_sub_page){

            add_menu_page( 
                $this->title, 
                $this->title, 
                'manage_options', 
                $this->slug, 
                array($this, 'render' ) 
            );

            return;
        }

        add_submenu_page( 
            'auto-share-content', 
            $this->title, 
            $this->title, 
            'manage_options', 
            $this->slug, 
            array($this, 'render' ) 
        );
    }

}
