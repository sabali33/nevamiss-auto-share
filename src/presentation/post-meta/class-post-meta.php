<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Post_Meta;

use Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Services\Network_Post_Manager;
use Nevamiss\Services\Network_Post_Provider;

class Post_Meta {

    public function __construct(
        private Factory                    $factory,
        private Network_Post_Provider      $network_post_provider,
    )
    {
    }

    public function meta_boxes(): void
    {
        $allowed_post_types = ['post'];

        add_meta_box('nevamiss-auto-share', __('Auto Share', 'nevamiss'), [$this, 'show_meta_box'], [$allowed_post_types]);
    }

    public function show_meta_box()
    {
        echo "Welcome to sharing instantly";
    }

    /**
     * @throws Exception
     */
    public function share_post_to_account(int $post_id, int $network_account_id): mixed
    {
        $data = $this->network_post_provider->format_post($post_id);

        [
            'account' => $network_account,
            'network_client' => $network_client
        ] = $this->network_post_provider->provide_network($network_account_id);

        $post_manager = $this->factory->new(
            Network_Post_Manager::class,
            $network_account,
            $network_client
        );

        return $post_manager->post($data);
    }

    /**
     * A function to share to many accounts on a single post.
     *
     * @param int $post_id
     * @param array $network_accounts
     * @return array
     * @throws Exception
     */
    public function share_post_to_accounts(int $post_id, array $network_accounts): array
    {

        $response = [];
        foreach( $network_accounts as $network_account ){
            $response[$network_account] = $this->share_post_to_account($post_id, $network_account);
        }
        return $response;
    }
}