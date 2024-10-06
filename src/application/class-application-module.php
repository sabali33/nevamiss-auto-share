<?php

namespace Nevamiss\Application;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Application\Compatibility\Version_Dependency_Provider;
use Nevamiss\Application\Post_Query\Query;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Services\Settings;
use Nevamiss\Services\WP_Cron_Service;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Application_Module implements ServiceModule, ExecutableModule {


	use ModuleClassNameIdTrait;

	public function services(): array {
		return array(
			DB::class                          => static function () {
				global $wpdb;
				return new DB( $wpdb );
			},
			Uninstall::class                   => static fn( ContainerInterface $container ) => new Uninstall(
				$container->get( DB::class ),
				$container->get( Settings::class ),
				$container->get( WP_Cron_Service::class ),
			),
			Query::class                       => fn() => new Query( new \WP_Query() ),
			Assets::class                      => fn() => new Assets(),
			Version_Dependency_Provider::class => fn() => new Version_Dependency_Provider(),
		);
	}

	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function run( ContainerInterface $container ): bool {
		/**
		 * @var Uninstall $shutdown
		 */
		$shutdown = $container->get( Uninstall::class );

		\register_deactivation_hook(
			NEVAMISS_ROOT,
			array( $shutdown, 'deactivate' )
		);

		$file = plugin_basename( NEVAMISS_ROOT );
		add_action( "uninstall_$file", array( $shutdown, 'run' ) );

		add_action( 'admin_enqueue_scripts', array( $container->get( Assets::class ), 'enqueue_script' ) );

		return true;
	}
}
