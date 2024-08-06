<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

interface Date_Interface {

	public function timestamp( string $date ): int;
}
