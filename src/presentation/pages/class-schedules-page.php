<?php

namespace Nevamiss\Presentation\Pages;

class Schedules_Page extends Page {

	public const TEMPLE_PATH = 'templates/schedules';
	/**
	 * @var true
	 */
	private bool $is_sub_page;

	public function __construct(
		public Schedules_Table_List $table_list,
		string $title,
		string $slug,
		string $filename,
		int $priority
	) {
		parent::__construct( $table_list, $title, $slug, $filename, $priority );
		$this->is_sub_page = true;
	}
}
