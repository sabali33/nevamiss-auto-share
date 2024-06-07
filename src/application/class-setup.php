<?php

declare(strict_types=1);

namespace Nevamiss\Application;

use Exception;
use function Nevamiss\error_notice;

class Setup {

	const MINIMUM_PHP_VERSION = '8.0';
	private static DB $db;
	private static ?Setup $instance = null;

	public function __construct( string $db ) {
		global $wpdb;

		static::$db = new $db( $wpdb );
	}

	public static function instance( string $db ): void {
		if ( self::$instance ) {
			return;
		}
		self::$instance = new self( $db );

		register_activation_hook(
			NEVAMISS_ROOT,
			array( self::$instance, 'activate' )
		);
	}

	/**
	 * @throws Exception
	 */
	public function activate(): void {
		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			throw new Exception( 'The server PHP version {PHP_VERSION} is not compatible', );
		}
		self::$db->setup_tables();
	}

	public function deactivate(): void {
		self::$db->drop_tables();
	}
}
