<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Contracts;

interface Update_Interface {

	public function update( int $id, array $data );
}
