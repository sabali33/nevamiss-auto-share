<?php
declare(strict_types=1);

namespace Nevamiss\Service;

use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Command_Query;
use Nevamiss\Domain\Repositories\Logger_Repository;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Posts_Stats_Repository;
use Nevamiss\Domain\Repositories\Schedule_Queue_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Domain\Repositories\Task_Repository;
use Psr\Container\ContainerInterface;

class Repositories_Module implements ServiceModule {

	use ModuleClassNameIdTrait;

	public function services(): array {
		global $wpdb;

		return array(
			Schedule_Repository::class        => fn( ContainerInterface $container ): Schedule_Repository => new Schedule_Repository(
				$container->get( Factory::class ),
				$wpdb
			),
			Posts_Stats_Repository::class     => fn( ContainerInterface $container ): Posts_Stats_Repository => new Posts_Stats_Repository(
				$container->get( Factory::class ),
				$wpdb
			),
			Schedule_Queue_Repository::class  => fn( ContainerInterface $container ): Schedule_Queue_Repository => new Schedule_Queue_Repository(
				$container->get( Factory::class ),
				$wpdb
			),
			Task_Repository::class            => fn( ContainerInterface $container ): Task_Repository => new Task_Repository(
				$container->get( Factory::class ),
				$wpdb
			),
			Logger_Repository::class          => fn( ContainerInterface $container ): Logger_Repository => new Logger_Repository(
				$container->get( Factory::class ),
				$wpdb
			),
			Network_Account_Repository::class => fn( ContainerInterface $container ): Network_Account_Repository => new Network_Account_Repository(
				$container->get( Factory::class ),
				$wpdb
			),
			Command_Query::class => function () {
				global $wpdb;
				return new Command_Query($wpdb);
			}
		);
	}
}
