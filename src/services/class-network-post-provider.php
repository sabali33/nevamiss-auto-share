<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Application\Post_Query\Query;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Schedule_Queue_Repository;
use Nevamiss\Networks\Contracts\Network_Clients_Interface;

class Network_Post_Provider {

	public function __construct(
		private Settings $settings,
		private Network_Account_Repository $account_repository,
		private Query $query,
		private Schedule_Queue_Repository $queue_repository,
		private array $network_clients,
	) {
	}

	/**
	 * @param Schedule $schedule
	 * @return array{data: string, account: Network_Account, network_client: Network_Clients_Interface}
	 * @throws Not_Found_Exception
	 */
	public function provide_instant_share_data( Schedule $schedule ): array {

		$schedule_accounts = $schedule->network_accounts();
		$schedule_posts    = $this->schedule_posts($schedule);

		$schedule_tags     = $schedule->social_media_tags();
		$data_set          = array();

		foreach ( $schedule_accounts as $schedule_account ) {

			foreach ( $schedule_posts as $schedule_post ) {

				$data = $this->format_post( $schedule_post );
				$data = $this->format_tags( $schedule_tags, $data );

				$data_set['data'] = $data;

				$data_set = array_merge(
					$data_set,
					$this->provide_network( $schedule_account )
				);
			}
		}

		return $data_set;
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function provide_network( int $account_id ): array {
		$network_account = $this->account_repository->get( $account_id );

		return array(
			'account'        => $network_account,
			'network_client' => $this->network_clients[ $network_account->network() ],
		);
	}

	/**
	 * @param \WP_Post|int $post
	 * @param null         $share_format
	 * @return string
	 */
	public function format_post( \WP_Post|int $post, $share_format = null ): string {
		if ( ! ( $post instanceof \WP_Post ) ) {
			$post = $this->query->post( $post );
		}
		global $wp_rewrite;

		//To make sure permalink function don't throw a warning
		if(!$wp_rewrite){
			$wp_rewrite = new \WP_Rewrite();
		}

		$default_share_format = <<< SHARE_FORMAT
            %TITLE%
            
            %LINK%

            %EXCERPT%

            %TAGS%
        SHARE_FORMAT;

		$share_format = $share_format ?? $default_share_format; // $this->settings->setting('post_share_format');

		$excerpt_length = $this->settings->setting( 'post_excerpt_length' );

		$excerpt = wp_trim_words( $post->post_content, $excerpt_length );

		$link = get_permalink( $post->ID );

		$output = str_replace( '%TITLE%', $post->post_title, $share_format );
		$output = str_replace( '%LINK%', $link, $output );

		return str_replace( '%EXCERPT%', $excerpt, $output );
	}

	private function format_tags( string $tags, $data ): array|string {
		return str_replace( '%TAGS%', $tags, $data );
	}

	/**
	 * @param Schedule $schedule
	 * @return array{class_identifier: string, parameters: array, schedule_id: int}
	 * @throws Not_Found_Exception
	 */
	public function provide_for_schedule(Schedule $schedule): array
	{
		$schedule_posts = $this->schedule_posts($schedule);

		/**
		 * @var Network_Account $schedule_accounts
		 */
		$schedule_accounts = $schedule->network_accounts();

		$post_data = array();

		foreach ( $schedule_accounts as $schedule_account ) {

			foreach ( $schedule_posts as $schedule_post ) {

				$post_data[] = array(
					'class_identifier' => Schedule_Tasks_Runner::class,
					'schedule_id'      => $schedule->id(),
					'parameters'       => array(
						'post_id'    => $schedule_post->ID,
						'account_id' => $schedule_account,
					),
				);
			}
		}

		return $post_data;
	}

	/**
	 * @throws Not_Found_Exception
	 */
	private function schedule_posts(Schedule $schedule): array
	{
		$posts_count = $schedule->query_args()['posts_per_page'];
		$schedule_queue = $this->queue_repository->get_schedule_queue_by_schedule_id($schedule->id());

		if(!$schedule_queue){
			error_log("Error from Nevamiss: unable to retrieve Schedule Queue");
			return [];
		}
		$post_ids = array_slice($schedule_queue->all_posts_ids(), 0, (int)$posts_count);

		return $this->query->query( ['post__in' => $post_ids]);
	}
}
