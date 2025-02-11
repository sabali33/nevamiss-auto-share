<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Infrastructure\Url_Shortner\Shortner_Collection;

class Url_Shortner_Manager {

	public function __construct( private Settings $settings, private Shortner_Collection $collection ) {
	}

	/**
	 * @throws \Exception
	 */
	public function on_post_publish( string $new_status, string $old_status, \WP_Post $post ) {

		if ( 'publish' !== $new_status ) {
			return;
		}

		if ( 'publish' === $old_status ) {
			return;
		}

		$post_id = $post->ID;

		$shortner_client_name = $this->settings->url_shortner();

		$shortner_client = $this->collection->get( $shortner_client_name );

		if ( ! $shortner_client ) {
			throw new \Exception( esc_html__( 'The Shortner client is not registered!', 'nevamiss' ) );
		}

		try {
			$response = $shortner_client->create( get_permalink( $post ), array() );

			update_post_meta( $post_id, '_nevamiss_short_url', $response );

		} catch ( \Throwable $throwable ) {
			do_action( Logger::GENERAL_LOGS, array( $throwable->getMessage(), true ), $post );
		}
	}
}
