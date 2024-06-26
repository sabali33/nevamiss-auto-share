<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Repositories\Schedule_Repository;

class Schedules_Table_List extends \WP_List_Table {

	public function __construct( private Schedule_Repository $schedule_repository, $args = array() ) {
		parent::__construct(
			array(
				'singular' => 'schedule',
				'plural'   => 'schedules',
				'screen'   => $args['screen'] ?? null,
			)
		);
		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
			$this->get_default_primary_column_name(),
		);
	}
	public function prepare_items(): void {
		$search             = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
		$schedules_per_page = 10;
		$paged              = $this->get_pagenum();

		$args = array(
			'per_page' => $schedules_per_page,
			'offset'   => ( $paged - 1 ) * $schedules_per_page,
			'search'   => $search,
		);

		if ( '' !== $args['search'] ) {
			$args['search'] = '*' . $args['search'] . '*';
		}

		if ( isset( $_REQUEST['orderby'] ) ) {
			$args['orderby'] = $_REQUEST['orderby'];
		}

		if ( isset( $_REQUEST['order'] ) ) {
			$args['order'] = $_REQUEST['order'];
		}

		$this->items = $this->schedule_repository->get_all( array() );

		$this->set_pagination_args(
			array(
				'total_items' => $this->schedule_repository->get_total(),
				'per_page'    => $schedules_per_page,
			)
		);
	}
	public function no_items(): void {
		_e( 'No Schedules found.', 'nevamiss' );
	}

	/**
	 * Gets a list of columns for the list table.
	 *
	 * @since 3.1.0
	 *
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns(): array {
		return array(
			'cb'               => '<input type="checkbox" />',
			'schedule_name'    => __( 'Name', 'nevamiss' ),
			'start_time'       => __( 'Start Time', 'nevamiss' ),
			'repeat_frequency' => __( 'Repeat Frequency', 'nevamiss' ),
			'query_args'       => __( 'Query Args', 'nevamiss' ),
			'network_accounts' => __( 'Network Accounts', 'nevamiss' ),
			'weekly_times'     => __( 'Weekly Times', 'nevamiss' ),
			'monthly_times'    => __( 'monthly Times', 'nevamiss' ),
			'daily_times'      => __( 'Daily Times', 'nevamiss' ),
			'created_at'       => __( 'Created At', 'nevamiss' ),
		);
	}

	protected function get_sortable_columns(): array {
		return array(
			'schedule_name' => array( 'Name', false, __( 'Name', 'nevamiss' ), __( 'Table ordered by Name.' ), 'asc' ),
			'created_at'    => array( 'Created Date', false, __( 'Created Date', 'nevamiss' ), __( 'Table ordered by Created Date.', 'nevamiss' ) ),
		);
	}

	public function display_rows(): void {
		foreach ( $this->items as  $schedule ) {
			echo "\n\t" . $this->single_row( $schedule );
		}
	}
	protected function get_default_primary_column_name(): string {
		return 'schedule_name';
	}

	public function column_schedule_name( array $item ): void
	{
		echo $item['schedule_name'];

		$actions = array_map(function($action){
			return $this->link($action);

		}, $this->action_list((int)$item['id']));

		echo $this->row_actions(
			$actions
		);
	}

	private function action_list(int $schedule_id): array
	{
		$nonce = wp_create_nonce('nevamiss_schedules');
		return array(
			array(
				'name' => 'edit',
				'label' => __('Edit', 'nevamiss'),
				'url' => admin_url("?page=edit-schedule&schedule_id=$schedule_id"),
				'class' => 'edit'
			),
			array(
				'name' => 'share',
				'label' => __('Share Next Posts Now', 'nevamiss'),
				'url' => admin_url("admin-post.php?schedule_id=$schedule_id&action=nevamiss_schedule_share&nonce=$nonce")
			),
			array(
				'name' => 'delete',
				'label' => __('Delete', 'nevamiss'),
				'url' => admin_url("admin-post.php?schedule_id=$schedule_id&action=nevamiss_schedule_delete&nonce=$nonce"),
				'class' => 'trash'
			),
			array(
				'name' => 'unschedule',
				'label' => __('Unschedule', 'nevamiss'),
				'url' => admin_url("admin-post.php?schedule_id=$schedule_id&action=nevamiss_schedule_unschedule&nonce=$nonce")
			)

		);
	}

	private function link(array $action): string
	{
		if(!isset($action['url'])){
			return '';
		}
		$title = $action['label'] ?? __('no label', 'nevamiss');
		$class = $action['class'] ?? '';
		return "<span class='$class'><a href='{$action['url']}' title='$title' class='$class'> $title</a></span>";
	}
}
