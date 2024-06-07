<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Services\Contracts\Date_Interface;

class Date implements Date_Interface {

	public function timestamp( string $date ): int {
		// TODO: Implement timestamp() method.
	}

	public function posting_time_in_week( array $week_days_time ): array {
	}

	public function posting_time_in_month( array $dates ): array {
		// TODO: Implement posting_time_in_month() method.
	}
}
