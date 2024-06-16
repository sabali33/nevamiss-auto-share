<?php

namespace Nevamiss\Presentation\Pages;

class Schedules_Page extends Page {

	public const TEMPLE_PATH = 'templates/schedules';
	const SLUG               = 'schedules';

	public function __construct( public Schedules_Table_List $table_list ) {
		parent::__construct(
			$table_list,
			'Schedules',
			self::SLUG,
			10,
			Auto_Share_Page::SLUG,
			true
		);
	}

	public function new_link(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		printf(
			'<a href="%1$s" class="page-title-action">%2$s</a>',
			esc_url( admin_url( '?page=edit-schedule' ) ),
			esc_html__( 'Add Schedule' )
		);
	}
}
