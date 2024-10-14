<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\Repositories\Schedule_Queue_Repository;
use Nevamiss\Presentation\Post_Meta\Post_Meta;
use function Nevamiss\sanitize_text_input_field;

class Ajax {

	public function __construct( private Post_Meta $post_meta, private Schedule_Queue_Repository $schedule_queue ) {
	}

	public function instant_posting_callback(): void {
		if ( ! $this->authorized( 'nevamiss-instant-share-action', sanitize_text_input_field( 'nonce', 'post' ) ) ) {// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_die( 'unauthorized' );
		}
		$post_id    = filter_input( INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$account_id = filter_input( INPUT_GET, 'account_id', FILTER_SANITIZE_NUMBER_INT );
		try {
			$this->post_meta->share_post_to_account( (int) $post_id, (int) $account_id );
			wp_send_json_success( 'Success', 202 );
		} catch ( \Exception $exception ) {
			wp_send_json_error( $exception->getMessage(), 401 );
		}

		die();
	}

	private function authorized( string $action, ?string $nonce ): bool {
		return isset( $nonce ) && wp_verify_nonce( $nonce, $action );
	}

	/**
	 * @throws \Exception
	 */
	public function sort_queue_posts_callback(): void {
		if ( ! $this->authorized( 'nevamiss_general_nonce', sanitize_text_input_field( 'nonce', 'post' ) ) ) {
			wp_send_json_error( esc_html__( 'Unauthorised', 'nevamiss' ), 401 );
			wp_die();
		}

		$schedule_id = (int) sanitize_text_input_field( 'scheduleId', 'post' );

		$posts = filter_input( INPUT_POST, 'data', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );

		$posts_filtered = array_filter(
			$posts,
			function ( $post ) {
				return ! empty( $post );
			}
		);

		if ( empty( $posts_filtered ) || count( $posts ) !== count( $posts_filtered ) ) {
			wp_send_json_error( esc_html__( 'Invalid post IDs provided', 'nevamiss' ), 401 );
			wp_die();
		}
		try {
			/**
			 * @var \Nevamiss\Domain\Entities\Schedule_Queue $queue
			 */
			$queue     = $this->schedule_queue->get_schedule_queue_by_schedule_id( $schedule_id );
			$old_posts = $queue->all_posts_ids();

			$remaining_posts_count = count( $queue->all_posts_ids() ) - count( $posts );
			$remaining_posts       = array_slice( $old_posts, -1, $remaining_posts_count );

			$ordered_posts = array( ...$posts, ...$remaining_posts );

			$this->schedule_queue->update( $queue->id(), array( 'all_posts_ids' => wp_json_encode( $ordered_posts ) ) );

			wp_send_json_success( 'Success', 202 );
		} catch ( \Throwable $throwable ) {
			wp_send_json_error( $throwable->getMessage(), 401 );
		}
		wp_die();
	}
}
