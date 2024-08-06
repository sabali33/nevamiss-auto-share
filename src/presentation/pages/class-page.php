<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Presentation\Contracts\Renderable;

abstract class Page implements Renderable {
	use Notices_Trait;

	protected string $title;
	protected int $priority;
	protected mixed $data;
	protected string $slug;
	protected string $filename;
	protected string $page_url;
	protected bool $is_sub_page = false;
	private ?string $parent;

	public function __construct(
		mixed $data,
		string $title,
		string $slug,
		int $priority,
		?string $parent = null,
		?bool $is_sub_page = false,
	) {
		$this->title       = $title;
		$this->priority    = $priority;
		$this->slug        = $slug;
		$this->parent      = $parent;
		$this->is_sub_page = $is_sub_page;
		$this->data        = $data;
		$this->page_url    = admin_url( "page=$slug" );
	}

	final public function render(): void {

		include NEVAMISS_PATH . 'resources/' . static::TEMPLE_PATH . '.php';
	}

	final public function register(): void {
		if ( ! $this->is_sub_page ) {

			add_menu_page(
				$this->title,
				$this->title,
				'manage_options',
				$this->slug,
				array( $this, 'render' )
			);

			return;
		}

		add_submenu_page(
			$this->parent,
			$this->title,
			$this->title,
			'manage_options',
			$this->slug,
			array( $this, 'render' )
		);
	}

	public function page_url(): string {
		return $this->page_url;
	}
}
