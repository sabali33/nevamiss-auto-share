<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Post_Meta;

use Exception;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Services\Network_Post_Manager;
use Nevamiss\Services\Network_Post_Provider;
use Nevamiss\Services\Settings;

class Post_Meta {

	public function __construct(
		private Factory $factory,
		private Network_Post_Provider $network_post_provider,
		private Settings $settings,
		private Network_Account_Repository $account_repository,
	) {
	}

	public function meta_boxes(): void {
		$allowed_post_types = $this->settings->allowed_post_types();

		add_meta_box( 'nevamiss-auto-share', __( 'Auto Share', 'nevamiss' ), array( $this, 'show_meta_box' ), array( $allowed_post_types ), 'side' );
	}

	public function show_meta_box( \WP_Post $post ): void {
		include NEVAMISS_PATH . 'resources/templates/post-meta.php';
	}

	/**
	 * @throws Exception
	 */
	public function share_post_to_account( int $post_id, int $network_account_id ): mixed {
		$data = $this->network_post_provider->format_post( $post_id );

		[
			'account' => $network_account,
			'network_client' => $network_client
		] = $this->network_post_provider->provide_network( $network_account_id );
		/**
		 * @var Network_Post_Manager $post_manager
		 */
		$post_manager = $this->factory->new(
			Network_Post_Manager::class,
			$network_account,
			$network_client
		);

		return $post_manager->post( $data );
	}

	/**
	 * A function to share to many accounts on a single post.
	 *
	 * @param int   $post_id
	 * @param array $network_accounts
	 * @return array
	 * @throws Exception
	 */
	public function share_post_to_accounts( int $post_id, array $network_accounts ): array {

		$response = array();
		foreach ( $network_accounts as $network_account ) {
			$response[ $network_account ] = $this->share_post_to_account( $post_id, $network_account );
		}
		return $response;
	}

	/**
	 * @return array<Network_Account>
	 */
	public function accounts(): array {
		return $this->account_repository->get_all();
	}
}
