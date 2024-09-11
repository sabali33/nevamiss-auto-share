<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\Repositories\Schedule_Queue_Repository;
use Nevamiss\Presentation\Post_Meta\Post_Meta;

class Ajax {

	public function __construct( private Post_Meta $post_meta, private Schedule_Queue_Repository $schedule_queue) {
	}

	public function instant_posting_callback(): void {
		if ( ! $this->authorized('nevamiss-instant-share-action', $_GET) ) {
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

	private function authorized(string $action, array $request): bool {
		return isset( $request['nonce'] ) && wp_verify_nonce( $request['nonce'], $action );
	}

	/**
	 * @throws \Exception
	 */
	public function sort_queue_posts_callback(): void
	{
		if( !$this->authorized('nevamiss_general_nonce', $_POST) ){
			wp_die('Unauthorised');
		}
		$data = [
			'schedule_id' => (int)filter_input( INPUT_POST, 'scheduleId', FILTER_SANITIZE_NUMBER_INT ),
			'posts' => filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ),
		];
		try{
			/**
			 * @var \Nevamiss\Domain\Entities\Schedule_Queue $queue
			 */
			$queue = $this->schedule_queue->get_schedule_queue_by_schedule_id($data['schedule_id']);
			$old_posts = $queue->all_posts_ids();

			$remaining_posts_count = count($queue->all_posts_ids()) - count($data['posts']);
			$remaining_posts = array_slice($old_posts, -1, $remaining_posts_count);

			$ordered_posts =  [...$data['posts'], ...$remaining_posts];

			$this->schedule_queue->update($queue->id(), ['all_posts_ids' => wp_json_encode($ordered_posts) ]);

			wp_send_json_success( 'Success', 202 );
		}catch (\Throwable $throwable){
			wp_send_json_error( $throwable->getMessage(), 401 );
		}
		wp_die();
	}
}
