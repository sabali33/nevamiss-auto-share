<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages\Tables;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Entities\Stats;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
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
		private Network_Account_Repository $account_repository,
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

	/**This function is redeclared because the name attribute of the select field needs to change.
	 *
	 * @param $which
	 * @return void
	 */
	protected function bulk_actions( $which = '' ) {
		if ( is_null( $this->_actions ) ) {
			$this->_actions = $this->get_bulk_actions();

			/**
			 * Filters the items in the bulk actions menu of the list table.
			 *
			 * The dynamic portion of the hook name, `$this->screen->id`, refers
			 * to the ID of the current screen.
			 *
			 * @since 3.1.0
			 * @since 5.6.0 A bulk action can now contain an array of options in order to create an optgroup.
			 *
			 * @param array $actions An array of the available bulk actions.
			 */
			$this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

			$two = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) ) {
			return;
		}

		echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' .
			/* translators: Hidden accessibility text. */
			esc_html__( 'Select bulk action', 'nevamiss' ) .
			'</label>';
		echo '<select name="bulk_action' . esc_attr( $two ) . '" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
		echo '<option value="-1">' . esc_html__( 'Bulk actions', 'nevamiss' ) . "</option>\n";

		foreach ( $this->_actions as $key => $value ) {
			if ( is_array( $value ) ) {
				echo "\t" . '<optgroup label="' . esc_attr( $key ) . '">' . "\n";

				foreach ( $value as $name => $title ) {
					$class = ( 'edit' === $name ) ? ' class="hide-if-no-js"' : '';

					echo "\t\t" . '<option value="' . esc_attr( $name ) . '"' . $class . '>' . esc_html( $title ) . "</option>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				echo "\t" . "</optgroup>\n";
			} else {
				$class = ( 'edit' === $key ) ? ' class="hide-if-no-js"' : '';

				echo "\t" . '<option value="' . esc_attr( $key ) . '"' . $class . '>' . esc_html( $value ) . "</option>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		echo "</select>\n";

		submit_button( esc_html__( 'Apply', 'nevamiss' ), 'action', '', false, array( 'id' => "doaction$two" ) );
		echo "\n";
	}
	public function prepare_items(): void {

		[$per_page, $args] = $this->query_args( array( 'search_field' => 'schedule_name' ) );

		$this->items = $this->schedule_repository->get_all( $args );

		$this->set_pagination_args(
			array(
				'total_items' => $this->schedule_repository->get_total(),
				'per_page'    => $per_page,
			)
		);
	}
	public function no_items(): void {
		esc_html_e( 'No Schedules found.', 'nevamiss' );
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
			'schedule_name'       => esc_html__( 'Name', 'nevamiss' ),
			'start_time'          => esc_html__( 'Start Time', 'nevamiss' ),
			'repeat_frequency'    => esc_html__( 'Repeat Frequency', 'nevamiss' ),
			'network_accounts'    => esc_html__( 'Network Accounts', 'nevamiss' ),
			'next_post'           => esc_html__( 'Next Post', 'nevamiss' ),
			'last_shared_posts'   => esc_html__( 'Last Shared Posts', 'nevamiss' ),
			'estimate_completion' => esc_html__( 'Estimated Completion date', 'nevamiss' ),

		);
	}

	protected function get_sortable_columns(): array {
		return array(
			'schedule_name' => array( 'schedule_name', false, esc_html__( 'Name', 'nevamiss' ), esc_html__( 'Table ordered by Name.', 'nevamiss' ) ),
			'created_at'    => array( 'created_at', false, esc_html__( 'Created Date', 'nevamiss' ), esc_html__( 'Table ordered by Created Date.', 'nevamiss' ) ),
		);
	}

	public function display_rows(): void {
		foreach ( $this->items as  $schedule ) {
			echo "\n\t" . $this->single_row( $schedule ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
	protected function get_default_primary_column_name(): string {
		return 'schedule_name';
	}


	protected function get_bulk_actions(): array {
		return $this->_bulk_actions();
	}

	public function current_action(): bool|string {
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return 'delete_all';
		}

		return parent::current_action();
	}

	/**
	 * @param Schedule $item
	 * @return void
	 */
	public function column_cb( $item ): void {
		$this->_column_cb( $item, 'schedules' );
	}

	public function column_schedule_name( Schedule $item ): void {
		echo esc_html( $item->name() );
	}

	/**
	 * @throws \Exception
	 */
	public function column_start_time( Schedule $schedule ): void {
		if ( ! $schedule->start_date() ) {
			echo esc_html( join( ',', $schedule->one_time_schedule() ) );
			return;
		}

		$date           = Date::create_from_format( $schedule->start_date() );
		$date_formatted = esc_html( $date->format() );
		$class_name     = $date->is_late() ? 'started' : 'not-started';

		echo "<span class='$class_name'> $date_formatted </span>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function column_repeat_frequency( Schedule $schedule ): void {
		echo esc_html( $schedule->repeat_frequency() );
	}
	public function column_network_accounts( Schedule $schedule ): void {
		$account_ids = join(',', $schedule->network_accounts());
		$accounts = $this->account_repository->get_by_ids( $account_ids );

		if(empty($accounts)){
			return;
		}
		$format_accounts = $this->format_accounts($accounts);
		echo esc_html( $format_accounts );
	}

	/**
	 */
	public function column_next_post( Schedule $schedule ): void {
		try {
			/**
			 * @var array{post_title: string, link: string } $posts
			 */
			$posts = $this->queue_service->schedule_posts( $schedule );

			foreach ( $posts as $post ) {
				echo wp_kses_post($this->link(
					array(
						'url'       => $post['link'],
						'label' => $post['post_title'],
					)
				)) . PHP_EOL;
			}
		} catch ( \Exception $exception ) {
			printf( '<i class="danger">%s</i>', esc_html( $exception->getMessage() ) );
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

		if ( empty( $schedule_stats ) ) {
			return;
		}
		$post_ids = array_map(
			function ( Stats $stat ) {
				return $stat->post_id();
			},
			$schedule_stats
		);

		$posts = $this->queue_service->posts_by_ids( $post_ids );
		foreach ( $posts as $post ) {
			echo $this->link( //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'url'   => $post['link'], //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'label' => $post['post_title'], //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				)
			) . PHP_EOL;
		}
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function column_estimate_completion( Schedule $schedule ): void {
		if ( $schedule->repeat_frequency() === 'none' ) {
			return;
		}
		$time_units = $this->queue_service->estimate_schedule_cycle_completion( $schedule );

		$message = __( 'Will complete a cycle in ', 'nevamiss' );

		$finish_date = $time_units['finish_date'];

		unset( $time_units['finish_date'] );

		$parts    = $this->format_estimate_message( $time_units );
		$message .= $parts;

		if ( empty( $parts ) ) {
			$message = __( 'No time estimates, too close', 'nevamiss' );
		}

		/* translators: %s: A finish date for the schedule */
		$message .= sprintf( __( ' ( on %s)', 'nevamiss' ), $finish_date );

		echo esc_html( $message );
	}

	private function format_estimate_message( $time_units ): string {
		$parts = array();
		foreach ( $time_units as $unit => $value ) {
			if ( $value > 0 ) {

				// $parts[] = sprintf( _n( "%s $unit", "%s ${unit}s", $value, 'nevamiss' ), $value );
				$parts[] = sprintf( $this->translate( $unit, (int) $value ), $value );
			} elseif ( ! empty( $parts ) ) {
				/*
				translators: %s: A time formatting string such as month, day, hour or minute */
				// $parts[] = sprintf( _n( "%s $unit", "%s ${unit}s", 0, 'nevamiss' ), 0 );
				$parts[] = $parts[] = sprintf( $this->translate( $unit, 0 ), 0 );
			}
		}

		if ( empty( $parts ) ) {
			return '';
		}

		return join( ', ', $parts );
	}
	private function action_list( Schedule $schedule ): array {

		$nonce       = wp_create_nonce( 'nevamiss_schedules' );
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

	public function translate( string $unit, int $value ) {

		$units = array(
			/* translators: %s: A time formatting string such as month, day, hour or minute */
			'day'    => _n( '%s day', '%s days', $value, 'nevamiss' ),
			/* translators: %s: A time formatting string such as month, day, hour or minute */
			'month'  => _n( '%s month', '%s months', $value, 'nevamiss' ),
			/* translators: %s: A time formatting string such as month, day, hour or minute */
			'hour'   => _n( '%s hour', '%s hours', $value, 'nevamiss' ),
			/* translators: %s: A time formatting string such as month, day, hour or minute */
			'minute' => _n( '%s minute', '%s minutes', $value, 'nevamiss' ),
		);
		return $units[ $unit ] ?? '';
	}

	private function format_accounts(array $accounts): string
	{
		return array_reduce($accounts, function(string $acc, array $account){
			$acc .= '- '. join(',', $account);
			return $acc;
		}, '');
	}
}
