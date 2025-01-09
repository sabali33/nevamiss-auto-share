<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

use Nevamiss\Domain\DTO\Share_Response;

interface Remote_Post_Interface {


	public function post( array $data ): mixed;
}
