<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages\Tables;

use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Services\Date;
use WP_List_Table;

class Network_Accounts_Table_List extends WP_List_Table {

	use Table_List_Trait;

	public function __construct(
		private Network_Account_Repository $account_repository,
		$args = array()
	) {
		parent::__construct(
			array(
				'singular' => 'network-account',
				'plural'   => 'network-accounts',
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

		[$per_page, $args] = $this->query_args( array( 'search_field' => 'name' ) );

		$this->items = $this->account_repository->get_all( $args );

		$this->set_pagination_args(
			array(
				'total_items' => $this->account_repository->get_total(),
				'per_page'    => $per_page,
			)
		);
	}
	public function no_items(): void {
		esc_html_e( 'No Accounts found, login above', 'nevamiss' );
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
			'cb'                => '<input type="checkbox" />',
			'name'              => esc_html__( 'Name', 'nevamiss' ),
			'network'           => esc_html__( 'Network', 'nevamiss' ),
			'remote_account_id' => esc_html__( 'Remote Account ID', 'nevamiss' ),
			'parent_remote_id'  => esc_html__( 'Parent Remote ID', 'nevamiss' ),
			'expires_in'        => esc_html__( 'Token Expires In', 'nevamiss' ),
			'created_at'        => esc_html__( 'Login At', 'nevamiss' ),

		);
	}

	protected function get_sortable_columns(): array {
		return array(
			'name'       => array( 'name', false, esc_html__( 'Name', 'nevamiss' ), esc_html__( 'Table ordered by Name.' ), 'asc' ),
			'created_at' => array( 'created_at', false, esc_html__( 'Created Date', 'nevamiss' ), esc_html__( 'Table ordered by Created Date.', 'nevamiss' ) ),
			'expires_in' => array( 'expires_in', false, esc_html__( 'Expire Date', 'nevamiss' ), esc_html__( 'Table ordered by expire date', 'nevamiss' ) ),
		);
	}

	protected function get_default_primary_column_name(): string {
		return 'name';
	}

	/**
	 * @param Network_Account $item
	 * @return void
	 */
	public function column_cb( $item ): void {
		$this->_column_cb( $item, 'network_accounts' );
	}

	public function column_name( Network_Account $account ): void {
		echo esc_html( $account->name() );
	}
	public function column_network( Network_Account $account ): void {
		echo esc_html( $account->network() );
	}
	public function column_remote_account_id( Network_Account $account ): void {
		echo esc_html( $account->remote_account_id() );
	}
	public function column_parent_remote_id( Network_Account $account ): void {
		echo esc_html( $account->parent_remote_id() );
	}

	/**
	 * @throws \Exception
	 */
	public function column_created_at( Network_Account $account ): void {
		$date = Date::create_from_format( $account->created_at(), 'Y-m-d H:i:s' );
		echo esc_html( $date->format( 'dS M Y @ H:i' ) );
	}

	/**
	 * @throws \Exception
	 */
	public function column_expires_in( Network_Account $account ): void {

		if ( ! $account->expires_in() && ! $account->parent_remote_id() ) {
			esc_html_e( 'No Expiry Date', 'nevamiss' );
			return;
		}
		if ( ! $account->expires_in() && $account->parent_remote_id() ) {
			$account = $this->account_repository->get_by_remote_id( $account->parent_remote_id() );
		}

		$date = Date::create_from_format( $account->expires_in(), 'Y-m-d h:i:s' );

		$time_diff = Date::now()->diff( $date );

		$style_classes = $this->style_classes( $time_diff );

		$months  = $time_diff->m;
		$days    = $time_diff->d;
		$minutes = $time_diff->i;
		$hours   = $time_diff->h;

		$expired_label = $time_diff->invert ? esc_html__( 'Expired since', 'nevamiss' ) : '';

		$output = '';

		if ( $months ) {
			/* translators: %s: Months count */
			$output .= sprintf( _n( '%s month', '%s months', $months, 'nevamiss' ), $months );
			$output .= ', ';
		}

		if ( $days ) {
			/* translators: %s: Days count */
			$output .= sprintf( _n( '%s day', '%s days', $days, 'nevamiss' ), $days );
			$output .= ', ';
		}

		if ( $hours ) {
			/* translators: %s: Hours count */
			$output .= sprintf( _n( '%s hour', '%s hours', $hours, 'nevamiss' ), $hours );
			$output .= ', ';
		}
		/* translators: %s: Minutes count */
		$output .= sprintf( _n( '%s minute', '%s minutes', $minutes, 'nevamiss' ), $minutes );

		/* translators: %1$s: Style class %2$s: Output string %3$s: Expire label */
		$message = wp_kses(
			sprintf(
				'<span class="%1$s">%3$s %2$s</span>',
				esc_attr( $style_classes ),
				esc_html( $output ),
				$expired_label
			),
			array(
				'span' => array(
					'class' => array()
				)
			)
		);

		if ( $time_diff->invert ) {

			$message = sprintf(
				wp_kses(
				/* translators: %1$s: Style class %2$s: Output string %3$s: Expire label */
					__( '<span class="%1$s">%3$s %2$s ago</span>', 'nevamiss' ),
					array(
						'span' => array(
							'class' => array()
						)
					)
				),
				esc_attr( $style_classes ),
				esc_html( $output ),
				$expired_label
			);
		}
		echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped
	}

	private function style_classes( \DateInterval $interval ): string {
		$classes = '';

		if ( $interval->invert ) {
			$classes .= 'expired-danger';
			return $classes;
		}

		if ( $interval->m === 0 && $interval->d < 10 ) {
			$classes .= 'about-to-expire ';
		}

		if ( $interval->m === 0 && $interval->d === 1 && $interval->h < 12 ) {
			$classes .= 'expiring-danger';
		}

		return $classes;
	}

	/**
	 * @param Network_Account $item
	 * @return array[]
	 */
	private function action_list( Network_Account $item ): array {
		$nonce = wp_create_nonce( 'nevamiss_network_accounts' );
		return array(
			array(
				'name'  => 'delete',
				'label' => __( 'Logout', 'nevamiss' ),
				'url'   => admin_url( "admin-post.php?account_id={$item->id()}&action=nevamiss_network_accounts_delete&nonce=$nonce" ),
				'class' => 'trash',
			),

		);
	}

	protected function get_bulk_actions(): array {
		return array_merge( $this->_bulk_actions(), array( 'delete_all' => __( 'Logout', 'nevamiss' ) ) );
	}

	public function repository(): Network_Account_Repository {
		return $this->account_repository;
	}
}
