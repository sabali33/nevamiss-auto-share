<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages\Tables;

use Nevamiss\Domain\Entities\Log;
use Nevamiss\Domain\Repositories\Logger_Repository;
use Nevamiss\Services\Date;

class Logs_Table_List extends \WP_List_Table {
	use Table_List_Trait;

	public function __construct(
		private Logger_Repository $repository,
		array $args = array()
	) {
		parent::__construct(
			array(
				'singular' => 'log',
				'plural'   => 'logs',
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

		[$per_page, $args] = $this->query_args( array( 'search_field' => 'messages' ) );

		$this->items = $this->repository->get_all( $args );

		$this->set_pagination_args(
			array(
				'total_items' => $this->repository->get_total(),
				'per_page'    => $per_page,
			)
		);
	}

	public function no_items(): void {
		esc_html_e( 'No Logs yet', 'nevamiss' );
	}
	public function get_columns(): array {
		return array(
			'messages'    => __( 'Message', 'nevamiss' ),
			'schedule_id' => __( 'Schedule ID', 'nevamiss' ),
			'posted_on'   => __( 'Shared Date', 'nevamiss' ),

		);
	}
	protected function get_sortable_columns(): array {
		return array(
			'posted_on' => array( 'posted_on', false, __( 'Shared Date', 'nevamiss' ), __( 'Table ordered by Posted Date.', 'nevamiss' ) ),
		);
	}
	protected function get_default_primary_column_name(): string {
		return 'messages';
	}

	protected function get_bulk_actions(): array {
		return array();
	}
	private function action_list( Log $item ): array {
		return array();
	}

	/**
	 * @throws \Exception
	 */
	public function column_posted_on( Log $log ): void {
		$date = Date::create_from_format( $log->posted_on(), 'Y-m-d H:i:s' );
		echo esc_html( $date->format() );
	}

	public function column_schedule_id( Log $log ): void {
		echo esc_html( $log->schedule_id() );
	}
	public function column_messages( Log $log ): void {
		echo esc_html( $log->messages() );
	}

	public function repository(): Logger_Repository {
		return $this->repository;
	}
}
