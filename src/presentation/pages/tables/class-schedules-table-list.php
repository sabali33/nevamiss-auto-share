<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages\Tables;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Entities\Stats;
use Nevamiss\Domain\Repositories\Posts_Stats_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Services\Date;
use Nevamiss\Services\Schedule_Queue as Schedule_Queue_Service;

class Schedules_Table_List extends \WP_List_Table {
    use Table_List_Trait;

	public function __construct(
		private Schedule_Repository $schedule_repository,
		private Posts_Stats_Repository $stats_repository,
		private Schedule_Queue_Service $queue_service,
		$args = array()
	) {
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
		$search             = $this->search_text();
		$schedules_per_page = 10;
		$paged              = $this->get_pagenum();

		$args = array(
			'per_page' => $schedules_per_page,
			'offset'   => ( $paged - 1 ) * $schedules_per_page,
			'search'   => ['schedule_name', $search],
		);

		if ( isset( $_REQUEST['orderby'] ) ) {
			$args['orderby'] = $_REQUEST['orderby'];
		}

		if ( isset( $_REQUEST['order'] ) ) {
			$args['order'] = $_REQUEST['order'];
		}

		$this->items = $this->schedule_repository->get_all( $args );

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
			'cb'                  => '<input type="checkbox" />',
			'schedule_name'       => __( 'Name', 'nevamiss' ),
			'start_time'          => __( 'Start Time', 'nevamiss' ),
			'repeat_frequency'    => __( 'Repeat Frequency', 'nevamiss' ),
			'network_accounts'    => __( 'Network Accounts', 'nevamiss' ),
			'next_post'           => __( 'Next Post', 'nevamiss' ),
			'last_shared_posts'   => __( 'Last Shared Posts', 'nevamiss' ),
			'estimate_completion' => __( 'Estimated Completion date', 'nevamiss' ),

		);
	}

	protected function get_sortable_columns(): array {
		return array(
			'schedule_name' => array( 'schedule_name', false, __( 'Name', 'nevamiss' ), __( 'Table ordered by Name.' ), 'asc' ),
			'created_at'    => array( 'created_at', false, __( 'Created Date', 'nevamiss' ), __( 'Table ordered by Created Date.', 'nevamiss' ) ),
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


	protected function get_bulk_actions(): array
	{
		return $this->_bulk_actions();
	}

	public function current_action(): bool|string
	{
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) {
			return 'delete_all';
		}

		return parent::current_action();
	}

	/**
	 * @param Schedule $item
	 * @return void
	 */
	public function column_cb( $item): void
	{
		$this->_column_cb($item, 'schedules');

	}

	public function column_schedule_name( Schedule $item ): void {
		echo $item->name();
	}

	public function column_start_time(Schedule $schedule): void
	{
        if(!$schedule->start_date()) {
            echo join(',', $schedule->one_time_schedule());
            return;
        }

        $date = Date::create_from_format($schedule->start_date());
        $date_formatted = $date->format('dS F Y ');
        $class_name = $date->is_late() ? 'started' : 'not-started';

        echo "<span class='$class_name'> $date_formatted </span>";
    }

	public function column_repeat_frequency( Schedule $schedule ): void {
		echo $schedule->repeat_frequency();
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function column_next_post( Schedule $schedule ): void {
		/**
		 * @var array{post_title: string, link: string } $posts
		 */
		$posts = $this->queue_service->schedule_posts( $schedule );

		foreach ( $posts as $post ) {
			echo $this->link(
				array(
					'url'   => $post['link'],
					'label' => $post['post_title'],
				)
			) . PHP_EOL;
		}
	}

	public function column_last_shared_posts( Schedule $schedule ): void {
		$schedule_stats = $this->stats_repository->get_all(
			array(
				'where'    => array(
					'schedule_id' => $schedule->id(),
				),
				'per_page' => intval( $schedule->query_args()['posts_per_page'] ),
			)
		);

		if(empty($schedule_stats)){
			return;
		}
		$post_ids       = array_map(
			function ( Stats $stat ) {
				return $stat->post_id();
			},
			$schedule_stats
		);

		$posts = $this->queue_service->posts_by_ids( $post_ids );
		foreach ( $posts as $post ) {
			echo $this->link(
				array(
					'url'   => $post['link'],
					'label' => $post['post_title'],
				)
			) . PHP_EOL;
		}
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function column_estimate_completion(Schedule $schedule): void
	{
        if($schedule->repeat_frequency() === 'none'){
            return;
        }
		$time_units = $this->queue_service->estimate_schedule_cycle_completion($schedule);

		$message = __('Will complete a cycle in ', 'nevamiss');

		$finish_date = $time_units['finish_date'];

		unset($time_units['finish_date']);

		$parts = $this->format_estimate_message($time_units);
		$message .= $parts;

		if(empty($parts)){
			$message = __('No time estimates, too close', 'nevamiss');
		}

		$message .= sprintf(__(' ( on %s)', 'nevamiss'), $finish_date);

		echo $message;
	}

	private function format_estimate_message($time_units): string
	{
		$parts = [];
		foreach ($time_units as $unit => $value) {
			if ($value > 0) {
				$parts[] = sprintf(_n("%s $unit", "%s ${unit}s", $value, 'nevamiss'), $value);
			} elseif (!empty($parts)) {
				$parts[] = sprintf(_n("%s $unit", "%s ${unit}s", 0, 'nevamiss'), 0);
			}
		}

		if(empty($parts)){
			return '';
		}

		return join(', ', $parts);

	}
	private function action_list( Schedule $schedule ): array {

		$nonce = wp_create_nonce( 'nevamiss_schedules' );
		$schedule_id = $schedule->id();

		return array(
			array(
				'name'  => 'edit',
				'label' => __( 'Edit', 'nevamiss' ),
				'url'   => admin_url( "?page=edit-schedule&schedule_id=$schedule_id" ),
				'class' => 'edit',
			),
			array(
				'name'  => 'share',
				'label' => __( 'Share Next Posts Now', 'nevamiss' ),
				'url'   => admin_url( "admin-post.php?schedule_id=$schedule_id&action=nevamiss_schedule_share&nonce=$nonce" ),
			),
			array(
				'name'  => 'delete',
				'label' => __( 'Delete', 'nevamiss' ),
				'url'   => admin_url( "admin-post.php?schedule_id=$schedule_id&action=nevamiss_schedule_delete&nonce=$nonce" ),
				'class' => 'trash',
			),
			array(
				'name'  => 'unschedule',
				'label' => __( 'Unschedule', 'nevamiss' ),
				'url'   => admin_url( "admin-post.php?schedule_id=$schedule_id&action=nevamiss_schedule_unschedule&nonce=$nonce" ),
			),

		);
	}
}
