<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

interface Logger_Interface {

	public function save( array $post_data ): void;
}
