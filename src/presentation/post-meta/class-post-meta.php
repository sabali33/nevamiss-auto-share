<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Post_Meta;

use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Factory\Factory;
use Nevamiss\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Instant_Post_Manager;
use Nevamiss\Services\Post_Formatter;

class Post_Meta {

    public function __construct(
        private readonly Network_Account_Repository $account,
        private readonly Factory                    $factory,
        private readonly Post_Formatter             $formatter,
        private readonly array                      $network_clients,
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
     * @throws \Exception
     */
    public function share_post_to_account(int $post_id, int $network_account_id): mixed
    {
        $network_account = $this->account->get($network_account_id);

        $data = $this->format_post($post_id);
        /**
         * @var Network_Clients_Interface $client
         */
        $network_client = $this->network_clients[$network_account->network()];

        $post_manager = $this->factory->new(
            Instant_Post_Manager::class,
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
     * @throws \Exception
     */
    public function share_post_to_accounts(int $post_id, array $network_accounts): array
    {

        $response = [];
        foreach( $network_accounts as $network_account ){
            $response[$network_account] = $this->share_post_to_account($post_id, $network_account);
        }
        return $response;
    }

    private function format_post(int $post_id): string
    {
        return $this->formatter->format($post_id);
    }

}