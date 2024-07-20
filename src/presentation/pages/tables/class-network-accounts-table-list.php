<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages\Tables;

use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Services\Date;

class Network_Accounts_Table_List extends \WP_List_Table {
    use Table_List_Trait;
	public function __construct(
		private Network_Account_Repository $account_repository,
		$args = array()
	) {
		parent::__construct(
			array(
				'singular' => 'Network Account',
				'plural'   => 'Network Accounts',
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

		$search = $this->search_text();
		$per_page = 10;
		$paged              = $this->get_pagenum();

		$args = array(
			'per_page' => $per_page,
			'offset'   => ( $paged - 1 ) * $per_page,
			'search'   => ['name', $search],
		);

		if ( isset( $_REQUEST['orderby'] ) ) {
			$args['orderby'] = $_REQUEST['orderby'];
		}

		if ( isset( $_REQUEST['order'] ) ) {
			$args['order'] = $_REQUEST['order'];
		}

		$this->items = $this->account_repository->get_all( $args );

		$this->set_pagination_args(
			array(
				'total_items' => $this->account_repository->get_total(),
				'per_page'    => $per_page,
			)
		);
	}
	public function no_items(): void {
		_e( 'No Accounts found, login above', 'nevamiss' );
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
			'name'       => __( 'Name', 'nevamiss' ),
			'network'          => __( 'Network', 'nevamiss' ),
			'remote_account_id'    => __( 'Remote Account ID', 'nevamiss' ),
			'parent_remote_id'    => __( 'Parent Remote ID', 'nevamiss' ),
			'created_at'           => __( 'Login At', 'nevamiss' ),

		);
	}

	protected function get_sortable_columns(): array {
		return array(
			'name' => array( 'name', false, __( 'Name', 'nevamiss' ), __( 'Table ordered by Name.' ), 'asc' ),
			'created_at'    => array( 'created_at', false, __( 'Created Date', 'nevamiss' ), __( 'Table ordered by Created Date.', 'nevamiss' ) ),
		);
	}

	protected function get_default_primary_column_name(): string {
		return 'name';
	}

	/**
	 * @param Network_Account $item
	 * @return void
	 */
	public function column_cb( $item): void
	{
        $this->_column_cb($item, 'network_accounts');
	}

	public function column_name(Network_Account $account): void
	{
		echo $account->name();
	}
	public function column_network(Network_Account $account): void
	{
		echo $account->network();
	}
	public function column_remote_account_id(Network_Account $account): void
	{
		echo $account->remote_account_id();
	}
	public function column_parent_remote_id(Network_Account $account): void
	{
		echo $account->parent_remote_id();
	}
	public function column_created_at(Network_Account $account): void
	{
        $date = Date::create_from_format($account->created_at(), 'Y-m-d H:i:s');
        echo $date->format('dS M Y @ H:i');
	}

	/**
	 * @param Network_Account $item
	 * @return array[]
	 */
	private function action_list(Network_Account $item): array
	{
		$nonce = wp_create_nonce( 'nevamiss_network_accounts' );
		return array(
			array(
				'name' => 'delete',
				'label' => __('Logout', 'nevamiss'),
				'url' => admin_url("admin-post.php?account_id={$item->id()}&action=nevamiss_network_accounts_delete&nonce=$nonce"),
				'class' => 'trash',
			),


		);
	}

	protected function get_bulk_actions(): array
	{
		return array_merge($this->_bulk_actions(), ['delete_all' => __('Logout', 'nevamiss')]);
	}
}