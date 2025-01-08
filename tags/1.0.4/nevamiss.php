<?php

declare(strict_types=1);

/**
 *  Plugin Name: Nevamiss Auto Share
 *  Plugin URI:  https://wordpress.org/plugins/nevamiss
 *  Description: A WordPress plugin to auto share content to social media Networks
 *  Author:      Eliasu Abraman
 *  Text Domain: nevamiss
 *  Domain Path: /languages
 *  License:     GPL v2 or later
 *  Requires    PHP: 8.0
 *  Version:     1.0.4
 */


namespace Nevamiss;

use Exception;
use Inpsyde\Modularity\Package;
use Inpsyde\Modularity\Properties\PluginProperties;
use Nevamiss\Application\Application_Module;
use Nevamiss\Infrastructure\Infrastructure_Module;
use Nevamiss\Presentation\Presentation_Module;
use Nevamiss\Service\Factory_Module;
use Nevamiss\Service\Repositories_Module;
use Nevamiss\Services\Services_Module;
use Throwable;

defined( 'ABSPATH' ) || die( 'Not authorized' );

define( 'NEVAMISS_PATH', plugin_dir_path( __FILE__ ) );
define( 'NEVAMISS_URL', plugin_dir_url( __FILE__ ) );
define( 'NEVAMISS_ROOT', __FILE__ );

/**
 * @throws Exception
 */
function autoload(): void {

	if ( ! file_exists( NEVAMISS_PATH . '/vendor/autoload.php' ) ) {
		throw new Exception( 'Autoload file can not be found' );
	}

	require_once NEVAMISS_PATH . '/vendor/autoload.php';
	require_once NEVAMISS_PATH . '/functions.php';
}

function error_notice( string $message ): void {
	foreach ( array( 'admin_notices', 'network_admin_notices' ) as $hook ) {
		add_action(
			$hook,
			static function () use ( $message ) {
				$class = 'notice notice-error';

				printf(
					'<div class="%1$s"><p>%2$s</p></div>',
					esc_attr( $class ),
					wp_kses_post( $message )
				);
			}
		);
	}
}

try {
	autoload();
	init();
} catch ( Exception $exception ) {

	error_notice( $exception->getMessage() );
}

/**
 * @throws Exception
 * @throws Throwable
 */
function plugin(): Package {
	static $package;

	if ( ! $package ) {
		$properties = PluginProperties::new( __FILE__ );
		$package    = Package::new( $properties );
		$package->
		addModule( new Application_Module() )->
		addModule( new Repositories_Module() )->
		addModule( new Presentation_Module() )->
		addModule( new Services_Module() )->
		addModule( new Factory_Module() )->
		addModule(new Infrastructure_Module());
	}

	return $package;
}

add_action(
	'plugins_loaded',
	static function (): void {

		try {
			plugin()->boot();
		} catch ( Throwable|\Exception $exception ) {
			error_notice( $exception->getMessage() );
		}
	}
);
