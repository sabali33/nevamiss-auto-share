<?php

declare(strict_types=1);

namespace Nevamiss\Application;

use Exception;
use Nevamiss\Services\WP_Cron_Service;

class Setup {

	private const MINIMUM_PHP_VERSION = '8.0';
	private static DB $db;
	private static ?Setup $instance = null;

	private function __construct(
		DB $db,
		private WP_Cron_Service $cron_service
	) {

		static::$db = $db;
	}

	public static function instance( DB $db, WP_Cron_Service $cron_service ): self {

		if ( self::$instance ) {
			return self::$instance;
		}

		self::$instance      = new self( $db,  $cron_service );

		register_activation_hook(
			NEVAMISS_ROOT,
			array( self::$instance, 'activate' )
		);

		return self::$instance;
	}

	/**
	 * @throws Exception
	 */
	public function activate(): bool {

		// Check for required PHP version
		$this->check_php_versions_compatibility();
		self::$db->setup_tables();

		$this->cron_service->schedule_all();

		return true;
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	private function check_php_versions_compatibility(): void {

		if ( version_compare(
			PHP_VERSION,
			self::MINIMUM_PHP_VERSION,
			'<'
		) ) {
			throw new Exception( 'The server PHP version {PHP_VERSION} is not compatible', );
		}
	}
}
