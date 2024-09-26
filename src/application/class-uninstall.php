<?php

declare(strict_types=1);

namespace Nevamiss\Application;

use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Services\Settings;
use Nevamiss\Services\WP_Cron_Service;

class Uninstall {

	private static DB $db;

	public function __construct(
		DB $db,
		private Settings $settings,
		private WP_Cron_Service $cron_service,
	)
	{
		static::$db = $db;
	}
	public function deactivate(): void {
		$this->cron_service->unschedule_all();
	}

	public function run(): void {

		if ( $this->settings->keep_records() ) {
			return;
		}

		self::$db->drop_tables();

		$this->settings->cleanup();
	}
}