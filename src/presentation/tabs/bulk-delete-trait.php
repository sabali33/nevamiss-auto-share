<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Domain\Entities\Stats;
use function Nevamiss\sanitize_text_input_field;


trait Bulk_Delete_Trait {

	public function bulk_delete( string $model_name ): void {

		if ( ! isset( $_REQUEST['action'] ) && ! isset( $_REQUEST['action2'] ) ) {
			return;
		}

		if ( $_REQUEST['action'] !== 'delete_all' || ! isset( $_REQUEST['model_name'] ) ) {
			return;
		}

		$nonce = sanitize_text_input_field('_wpnonce');

		if ( ! wp_verify_nonce( $nonce, "bulk-$model_name" ) ) {
			return;
		}
		$translated_model_name = str_replace( '-', '_', $model_name );

		[$translated_model_name => $models] = filter_input_array(
			INPUT_GET,
			array(
				$translated_model_name => array(
					'filter' => FILTER_VALIDATE_INT,
					'flags'  => FILTER_REQUIRE_ARRAY,
				),
			)
		);
		$results                            = array(
			'success' => array(),
			'error'   => array(),
		);

		foreach ( $models as $model_id ) {
			try {
				/**
				 * @var Network_Account|Stats $model
				 */
				$model = $this->table_list->repository()->get( $model_id );

				$deleted = match ( get_class( $model ) ) {
					Network_Account::class => $this->delete_network_accounts( $model, $model_id ),
					Stats::class => $this->delete_stats( $model_id )
				};

				$results['success'][ $model_id ] = $deleted;

			} catch ( \Exception $exception ) {
				$results['error'][ $model_id ] = $exception->getMessage();
			}
		}
		if ( ! empty( $results['error'] ) ) {
			$stats_ids = join( ', ', array_keys( $results['error'] ) );
			$this->redirect(
				array(
					'status'  => 'error',
					'message' => "Unable to delete $stats_ids",
				)
			);
			exit;
		}
		$model_ids = join( ', ', array_keys( $results['success'] ) );
		$this->redirect(
			array(
				'status'  => 'success',
				'message' => "Deleted the data: $model_ids",
			)
		);
		exit;
	}

	/**
	 * @param Network_Account|Stats $model
	 * @param mixed                 $model_id
	 * @return bool|string
	 * @throws \Exception
	 */
	private function delete_network_accounts( Network_Account|Stats $model, mixed $model_id ): string|bool {
		if ( $model->parent_remote_id() ) {
			$deleted = $this->table_list->repository()->delete( $model_id );
		} else {
			$children_accounts = $this->table_list->repository()->get_all(
				array(
					'where' => array(
						'parent_remote_id' => $model->remote_account_id(),
					),
				)
			);

			$all_accounts = array( $model, ...$children_accounts );
			$deleted      = '';

			/**
			 * @var Network_Account $account
			 */
			foreach ( $all_accounts as $account ) {
				$deleted .= $this->table_list->repository()->delete( $account->id() );
			}
		}
		return $deleted;
	}

	private function delete_stats( int $model_id ) {
		return $this->table_list->repository()->delete( $model_id );
	}
}
