<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Application\Post_Query\Query;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Schedule_Queue_Repository;
use Nevamiss\Infrastructure\Url_Shortner\URL_Shortner_Interface;
use Nevamiss\Infrastructure\Url_Shortner\Url_Shortner_Response;
use Nevamiss\Networks\Contracts\Network_Clients_Interface;

class Network_Post_Provider {

	public function __construct(
		private Settings $settings,
		private Network_Account_Repository $account_repository,
		private Query $query,
		private Schedule_Queue_Repository $queue_repository,
		private array $network_clients,
		private Factory $factory,
	) {
	}

	/**
	 * @param Schedule $schedule
	 * @return array{data: string, account: Network_Account, network_client: Network_Clients_Interface}
	 * @throws Not_Found_Exception
	 */
	public function provide_instant_share_data( Schedule $schedule ): array {

		$schedule_accounts = $schedule->network_accounts();
		$schedule_posts    = $this->schedule_posts( $schedule );

		$schedule_tags = $schedule->social_media_tags();
		$data_set      = array();

		foreach ( $schedule_accounts as $schedule_account ) {
			/**
			 * @var \WP_Post $schedule_post
			 */
			foreach ( $schedule_posts as $schedule_post ) {

				$data = $this->format_post( $schedule_post );

				$data['status_text'] = $this->format_tags( $schedule_tags, $data['status_text'] );

				$data_unit  = array(
					'data' => $data,
				);
				$data_set[] = array_merge( $data_unit, $this->provide_network( $schedule_account ) );
			}
		}

		return $data_set;
	}

	/**
	 * @throws Not_Found_Exception
	 * @throws \Exception
	 */
	public function provide_network( int $account_id ): array {
		/**
		 * @var Network_Account $network_account
		 */
		$network_account = $this->account_repository->get( $account_id );

		if ( ! $network_account->token() ) {
			$network_account = $this->new_network_account( $network_account );
		}
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
	public function format_post( int|\WP_Post $post, $share_format = null ): array {

		if ( ! ( $post instanceof \WP_Post ) ) {
			$post = $this->query->post( $post );
		}
		global $wp_rewrite;

		// To make sure permalink function don't throw a warning
		if ( ! $wp_rewrite ) {
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

		/**
		 * @var Url_Shortner_Response $short_url
		 */
		$short_url = get_post_meta($post->ID, '_nevamiss_short_url', true);

		$url = $short_url ? $short_url->short_url() : get_permalink( $post->ID );

		$output = str_replace( '%TITLE%', $post->post_title, $share_format );
		$output = str_replace( '%LINK%', $url, $output );
		$output = str_replace( '%EXCERPT%', $excerpt, $output );

		$data              = array( 'status_text' => $output );
		$data['image_url'] = get_the_post_thumbnail_url( $post->ID );
		$data['title']     = $post->post_title;
		$data['excerpt']   = $post->post_excerpt;
		$data['link']      =  $url;

		return $data;
	}

	private function format_tags( string $tags, $data ): array|string {
		return str_replace( '%TAGS%', $tags, $data );
	}

	public function format_instant_share_post(int|\WP_Post $post, string $tags= '')
	{
		$content = $this->format_post($post);
		return $this->format_tags($tags, $content);

	}

	/**
	 * @param Schedule $schedule
	 * @return array{class_identifier: string, parameters: array, schedule_id: int}
	 * @throws Not_Found_Exception
	 */
	public function provide_for_schedule( Schedule $schedule ): array {
		$schedule_posts = $this->schedule_posts( $schedule );

		/**
		 * @var Network_Account $schedule_accounts
		 */
		$schedule_accounts = $schedule->network_accounts();

		$post_data = array();

		foreach ( $schedule_accounts as $schedule_account ) {

			foreach ( $schedule_posts as $schedule_post ) {

				$post_data[] = array(
					'class_identifier' => Network_Post_Manager::class,
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
	private function schedule_posts( Schedule $schedule ): array {
		$posts_count    = $schedule->query_args()['posts_per_page'];
		$schedule_queue = $this->queue_repository->get_schedule_queue_by_schedule_id( $schedule->id() );

		if ( ! $schedule_queue ) {
			throw new \RuntimeException('Error from Nevamiss: unable to retrieve Schedule Queue');
		}
		$remaining_queue_posts_count = $schedule_queue->post_count_to_share();

		$post_ids = array_slice( $schedule_queue->all_posts_ids(), 0, (int) $posts_count );

		if ( count( $post_ids ) > $remaining_queue_posts_count && ! $this->settings->repeat_cycle() ) {
			$post_ids = array_slice( $post_ids, 0, $remaining_queue_posts_count );
		}
		return $this->query->query( array( 'post__in' => $post_ids ) );
	}

	/**
	 * @param Network_Account $network_account
	 * @return Network_Account
	 * @throws \Exception
	 */
	private function new_network_account( Network_Account $network_account ): Network_Account {

		$parent = $this->account_repository->get_all( array( 'where' => array( 'remote_account_id' => $network_account->parent_remote_id() ) ) );
		if ( empty( $parent ) ) {
			throw new \Exception( 'Token is empty' );
		}
		/**
		 * @var Network_Account $parent
		 */
		[$parent] = $parent;

		$network_account_arr = array_merge( $network_account->to_array(), array( 'token' => $parent->token() ) );

		return $this->factory->new( Network_Account::class, $network_account_arr );
	}
}
