<?php

namespace Nevamiss\Domain\Entities;

class Network_Account
{
    public function __construct(
        private int    $id,
        private string $name,
        private string $remote_account_id,
        private string $token,
        private string $network
    )
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function remote_account_id(): string
    {
        return $this->remote_account_id;
    }

    public function id(): int
    {
        return $this->id;
    }
    public function token(): string
    {
        return $this->token;
    }
    public function network(): string
    {
        return $this->network;
    }

}