<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

use Nevamiss\Domain\DTO\Share_Response;
use Nevamiss\Domain\Entities\Network_Account;

interface Remote_Post_Interface
{
    /**
     * @param int $id A schedule ID in the case of Schedule Post Manager class and post ID in the case of instant share.
     * @return mixed
     */
    public function run(int $id): mixed;

    public function post(string $data): Share_Response;

}