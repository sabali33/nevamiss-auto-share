<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Contracts;

interface Get_One_Interface
{
    public function get(int $id);
}