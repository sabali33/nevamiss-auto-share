<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Services\Contracts\Remote_Post_Interface;

class Schedule_Post_Manager implements Remote_Post_Interface {

    public function __construct(private readonly Network_Account $account)
    {

    }
    /**
     * @param int $id Schedule ID
     * @return mixed
     */
    public function run(int $id): mixed
    {
        // TODO: Implement run() method.
    }

    public function post(string $data): \Nevamiss\Domain\DTO\Share_Response
    {
        // TODO: Implement post() method.
    }
}