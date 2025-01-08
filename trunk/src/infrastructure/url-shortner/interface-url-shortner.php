<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Url_Shortner;

interface URL_Shortner_Interface {

	public function create( string $url ): Url_Shortner_Response;

	public function id();
	public function label();

	public function settings_fields( array $settings_values ): array;
}
