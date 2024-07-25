<?php

namespace Nevamiss\Application;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Application\Post_Query\Query;
use Nevamiss\Services\Settings;
use Psr\Container\ContainerInterface;

class Application_Module implements ServiceModule, ExecutableModule {


	use ModuleClassNameIdTrait;

	public function services(): array {
		return array(
			DB::class     => static function () {
				global $wpdb;
				return new DB( $wpdb );
			},
			Setup::class  => static fn(ContainerInterface $container) => new Setup(
				DB::class, $container->get(Settings::class) ),
			Query::class  => fn() => new Query( new \WP_Query() ),
			Assets::class => fn() => new Assets(),
		);
	}

	public function run( ContainerInterface $container ): bool {

		register_deactivation_hook(
			NEVAMISS_ROOT,
			array( $container->get( Setup::class ), 'deactivate' )
		);

		add_action( 'admin_enqueue_scripts', array( $container->get( Assets::class ), 'enqueue_script' ) );

		return true;
	}
}
