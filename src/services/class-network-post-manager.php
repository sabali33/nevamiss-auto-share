<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\DTO\Share_Response;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Contracts\Remote_Post_Interface;

class Network_Post_Manager implements Remote_Post_Interface {

    public function __construct(
        private Network_Account $account,
        private Network_Clients_Interface $network_client,
        private Post_Formatter $formatter
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
        return $this->network_client->post($data, $this->account);
    }

}