<?php

declare(strict_types=1);

namespace Nevamiss\Application;

use Exception;
use Nevamiss\Application\Compatibility\Version_Dependency_Provider;
use Nevamiss\Application\Compatibility\Versions_Dependency_Interface;
use Nevamiss\Services\Settings;
use function Nevamiss\error_notice;

class Setup {

	private const MINIMUM_PHP_VERSION = '8.0';
	private static DB $db;
	private static ?Setup $instance = null;

	public function __construct(
		DB $db,
		private Versions_Dependency_Interface $versions_dependencies,
		private ?Settings $settings
	) {

		static::$db = $db;
	}

	public static function instance( \wpdb $wpdb ): void {
		if ( self::$instance ) {
			return;
		}
		$db                  = new DB( $wpdb );
		$dependency_provider = new Version_Dependency_Provider();
		self::$instance      = new self( $db, $dependency_provider, null );

		register_activation_hook(
			NEVAMISS_ROOT,
			array( self::$instance, 'activate' )
		);
	}

	/**
	 * @throws Exception
	 */
	public function activate(): bool {
		// Check for required PHP version
		$this->check_php_versions_compatibility();
		self::$db->setup_tables();
		return true;
	}

	public function deactivate(): void {

		if ( $this->settings->keep_records() ) {
			return;
		}
		self::$db->drop_tables();
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	private function check_php_versions_compatibility(): void {

		if ( version_compare(
			$this->versions_dependencies->php_version(),
			self::MINIMUM_PHP_VERSION,
			'<'
		) ) {
			throw new Exception( 'The server PHP version {PHP_VERSION} is not compatible', );
		}
	}
}
