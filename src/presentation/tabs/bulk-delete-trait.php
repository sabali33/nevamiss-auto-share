<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

use Exception;

trait Bulk_Delete_Trait {

	public function bulk_delete( string $model_name ): void {
		if ( ! isset( $_REQUEST['action'] ) && ! isset( $_REQUEST['action2'] ) ) {
			return;
		}

		if ( $_REQUEST['action'] !== 'delete_all' || ! isset( $_REQUEST[ $model_name ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], "bulk-$model_name" ) ) {
			return;
		}

		[$model_name => $models] = filter_input_array(
			INPUT_GET,
			array(
				$model_name => array(
					'filter' => FILTER_VALIDATE_INT,
					'flags'  => FILTER_REQUIRE_ARRAY,
				),
			)
		);
		$results                 = array(
			'success' => array(),
			'error'   => array(),
		);

		foreach ( $models as $model ) {
			try {
				$deleted                      = $this->table_list->repository()->delete( $model );
				$results['success'][ $model ] = $deleted;
			} catch ( Exception $exception ) {
				$results['error'][ $model ] = $exception->getMessage();
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
		$stats_ids = join( ', ', array_keys( $results['success'] ) );
		$this->redirect(
			array(
				'status'  => 'success',
				'message' => "Deleted the data: $stats_ids",
			)
		);
		exit;
	}
}
