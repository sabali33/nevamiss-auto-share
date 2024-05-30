<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

interface Remote_Post_Interface
{
    public function run(int $task_id): void;

    public function post(int $task_id): void;

}