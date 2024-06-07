<?php

declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

interface Logger_Interface {

	public function channels(): array;
	public function messages(): array;
	public function save(): void;
}
