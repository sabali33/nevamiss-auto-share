<?php

declare(strict_types=1);

namespace Nevamiss;

use Nevamiss\Application\DB;
use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Application\Setup;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Services\WP_Cron_Service;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

defined( 'ABSPATH' ) || die( 'Not authorized' );

/**
 * @throws Throwable
 */
function container(): ContainerInterface {
	return plugin()->container();
}

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 * @throws Throwable
 */
function factory(): Factory {
	return container()->get( Factory::class );
}

/**
 * @throws Not_Found_Exception
 * @throws NotFoundExceptionInterface
 * @throws Throwable
 * @throws ContainerExceptionInterface
 */
function component( string $class, array $attributes, array $inner_components = array() ): Component {
	return \Nevamiss\factory()->component( $class, $attributes, $inner_components );
}

function init(): void {
	global $wpdb;
	$db   = new DB( $wpdb );
	$cron = new WP_Cron_Service( new Schedule_Repository( new Factory(), $wpdb ) );
	Setup::instance( $db, $cron );
}

function sanitize_text_input_field( string $field, string $method = 'get' ): ?string {

	return match($method){
		'post' => isset($_POST[$field]) ? \wp_unslash( sanitize_text_field( $_POST[$field] ) ) : null,  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		'request' => isset($_REQUEST[$field]) ? \wp_unslash( sanitize_text_field( $_REQUEST[$field] ) ) : null, // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		default => isset($_GET[$field]) ? \wp_unslash( sanitize_text_field( $_GET[$field] ) ) : null, // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	};
}
