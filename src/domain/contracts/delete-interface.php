<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Contracts;

interface Delete_Interface
{
    public function delete(int $id);
}