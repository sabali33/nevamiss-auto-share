<?php

namespace Nevamiss\Presentation\Pages;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Application\Post_Query\Query;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Posts_Stats_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Networks\Media_Network_Collection;
use Nevamiss\Presentation\Post_Meta\Post_Meta;
use Nevamiss\Presentation\Tabs\General_Tab;
use Nevamiss\Presentation\Tabs\Logs_Tab;
use Nevamiss\presentation\Tabs\Network_Accounts_Tab;
use Nevamiss\Presentation\Tabs\Stats_Tab;
use Nevamiss\Presentation\Tabs\Tab_Collection;
use Nevamiss\Presentation\Tabs\Tab_Collection_Interface;
use Nevamiss\Presentation\Tabs\Tab_Interface;
use Nevamiss\Services\Form_Validator;
use Nevamiss\Services\Schedule_Queue;
use Nevamiss\Services\Settings;
use Nevamiss\Services\Network_Post_Provider;
use Psr\Container\ContainerInterface;

class Presentation_Module implements ServiceModule, ExecutableModule {

	use ModuleClassNameIdTrait;

	public function services(): array {
		return array(

			Schedules_Page::class       => static function ( ContainerInterface $container ) {

				return new Schedules_Page(
					$container->get( Schedules_Table_List::class ),
					$container->get(Schedule_Repository::class)
				);
			},

			Settings_Page::class        => static function ( ContainerInterface $container ) {
				return new Settings_Page(
					$container->get( Settings::class ),
					$container->get( Media_Network_Collection::class ),
					$container->get(Tab_Collection::class),
				);
			},

			Stats_Page::class           => static fn ( ContainerInterface $container ) => new Stats_Page(
				$container->get( Posts_Stats_Repository::class )
			),

			Schedule_View_Page::class   => function ( ContainerInterface $container ): Schedule_View_Page {

				return new Schedule_View_Page( $container->get( Schedule_Repository::class ) );
			},
			Schedule_Form::class        => function ( ContainerInterface $container ): Schedule_Form {

				return new Schedule_Form(
					$container->get( Schedule_Repository::class ),
					$container->get( Network_Account_Repository::class ),
					$container->get( Form_Validator::class ),
					$container->get( Factory::class )
				);
			},

			Auto_Share_Page::class      => fn (): Auto_Share_Page => new Auto_Share_Page( array() ),

			Post_Meta::class            => function ( ContainerInterface $container ) {
				return new Post_Meta(
					$container->get( Factory::class ),
					$container->get( Network_Post_Provider::class ),
				);
			},
			Schedules_Table_List::class => fn( ContainerInterface $container ) => new Schedules_Table_List(
				$container->get( Schedule_Repository::class ),
				$container->get( Posts_Stats_Repository::class ),
				$container->get( Schedule_Queue::class ),
			),
			Tab_Collection_Interface::class => function(ContainerInterface $container) {
				$factory = $container->get(Factory::class);
				return [
					new General_Tab($factory),
					new Network_Accounts_Tab($factory),
					new Stats_Tab($factory),
					new Logs_Tab($factory),
				];
			},
			Tab_Collection::class => function (ContainerInterface $container) {
				$collection = new Tab_Collection();
				/**
				 * @var Tab_Interface $tab
				 */
				foreach ($container->get(Tab_Collection_Interface::class) as $tab){
					$collection->register($tab->slug(), $tab);
				}
				return $collection;
			}
		);
	}

	public function run( ContainerInterface $container ): bool {
		add_action(
			'admin_menu',
			static function () use ( $container ) {
				$container->get( Auto_Share_Page::class )->register();
				$container->get( Schedules_Page::class )->register();
				$container->get( Settings_Page::class )->register();
				$container->get( Schedule_View_Page::class )->register();
				$container->get( Stats_Page::class )->register();
				$container->get( Schedule_Form::class )->register();
			}
		);

		add_action(
			'add_meta_boxes',
			array( $container->get( Post_Meta::class ), 'meta_boxes' )
		);

		return true;
	}
}
