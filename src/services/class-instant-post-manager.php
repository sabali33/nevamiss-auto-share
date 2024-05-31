<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\Covariance\Share_Response;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Contracts\Remote_Post_Interface;

class Instant_Post_Manager implements Remote_Post_Interface {

    public function __construct(
        private readonly Network_Account $account,
        private readonly Network_Clients_Interface           $network_client,
        private readonly Post_Formatter $formatter
    )
    {

    }

    /**
     * @param int $id Post ID.
     * @return Share_Response
     */
    public function run(int $id): Share_Response
    {
        $data = $this->formatter->format($id);

        return $this->post($data);
    }

    public function post(string $data): Share_Response
    {
        $client = $this->network_client;

        $response = $client->post($data, $this->account);

        return new Share_Response($response);
    }

}