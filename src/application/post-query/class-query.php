<?php

namespace Nevamiss\Application\Post_Query;

use WP_Post;

class Query {

	public function __construct( private \WP_Query $wp_query ) {
	}

	public function query( array $args ): array {
		return $this->wp_query->query( $args );
	}

	public function post( int $post_id ): WP_Post {
		return get_post( $post_id );
	}
}
