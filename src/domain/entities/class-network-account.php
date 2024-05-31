<?php

namespace Nevamiss\Domain\Entities;

class Network_Account
{
    public function __construct(
        private readonly string $token,
        private readonly string $network
    )
    {
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