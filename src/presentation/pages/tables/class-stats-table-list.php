<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages\Tables;

use Nevamiss\Domain\Entities\Stats;
use Nevamiss\Domain\Repositories\Posts_Stats_Repository;
use Nevamiss\Services\Date;

class Stats_Table_List extends \WP_List_Table {
	use Table_List_Trait;

	public function __construct(
		private Posts_Stats_Repository $stats_repository,
		array $args = array()
	)
	{
		parent::__construct(
			array(
				'singular' => 'stats',
				'plural'   => 'stats',
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

		[$per_page, $args] = $this->query_args(['search_field' => ' remote_post_id']);

		$this->items = $this->stats_repository->get_all( $args );

		$this->set_pagination_args(
			array(
				'total_items' => $this->stats_repository->get_total(),
				'per_page'    => $per_page,
			)
		);
	}

	public function no_items(): void {
		_e( 'No data yet', 'nevamiss' );
	}
	public function get_columns(): array {
		return array(
			'cb'                  => '<input type="checkbox" />',
			'remote_post_id'    => __( 'Remote Post ID', 'nevamiss' ),
			'schedule_id'       => __( 'Schedule ID', 'nevamiss' ),
			'post_id'    => __( 'Post ID', 'nevamiss' ),
			'posted_on'          => __( 'Shared Date', 'nevamiss' ),

		);
	}
	protected function get_sortable_columns(): array {
		return array(
			'posted_on'    => array( 'posted_on', false, __( 'Shared Date', 'nevamiss' ), __( 'Table ordered by Posted Date.', 'nevamiss' ) ),
		);
	}
	protected function get_default_primary_column_name(): string {
		return 'remote_post_id';
	}
	public function column_cb( $item): void
	{
		$this->_column_cb($item, 'stats');
	}
	protected function get_bulk_actions(): array
	{
		return array_merge($this->_bulk_actions(), ['delete_all' => __('Delete', 'nevamiss')]);
	}
	private function action_list(Stats $item): array
	{
		$nonce = wp_create_nonce( 'nevamiss_stats' );
		return array(
			array(
				'name' => 'delete',
				'label' => __('Delete', 'nevamiss'),
				'url' => admin_url("admin-post.php?entry_id={$item->id()}&action=nevamiss_stats_delete&nonce=$nonce"),
				'class' => 'trash',
			),
		);
	}
	public function column_posted_on(Stats $account): void
	{
		$date = Date::create_from_format($account->posted_on(), 'Y-m-d H:i:s');
		echo $date->format('dS M Y @ H:i');
	}

	public function column_schedule_id(Stats $stats): void
	{
		echo $stats->schedule_id();
	}
	public function column_post_id(Stats $stats): void
	{
		echo $stats->post_id();
	}
	public function column_remote_post_id(Stats $stats): void
	{
		echo $stats->remote_post_id();
	}

	public function repository(): Posts_Stats_Repository
	{
		return $this->stats_repository;
	}

}