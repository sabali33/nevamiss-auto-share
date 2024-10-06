<?php

namespace Nevamiss\Presentation;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Logger_Repository;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Posts_Stats_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Infrastructure\Networks\Media_Network_Collection;
use Nevamiss\Infrastructure\Url_Shortner\Shortner_Collection;
use Nevamiss\Presentation\Pages\Auto_Share_Page;
use Nevamiss\Presentation\Pages\Schedule_Form;
use Nevamiss\Presentation\Pages\Schedule_View_Page;
use Nevamiss\Presentation\Pages\Schedules_Page;
use Nevamiss\Presentation\Pages\Settings_Page;
use Nevamiss\Presentation\Pages\Suggestions_Page;
use Nevamiss\Presentation\Pages\Tables\Logs_Table_List;
use Nevamiss\Presentation\Pages\Tables\Network_Accounts_Table_List;
use Nevamiss\Presentation\Pages\Tables\Schedules_Table_List;
use Nevamiss\Presentation\Pages\Tables\Stats_Table_List;
use Nevamiss\Presentation\Post_Meta\Post_Meta;
use Nevamiss\Presentation\Tabs\General_Tab;
use Nevamiss\Presentation\Tabs\Logs_Tab;
use Nevamiss\Presentation\Tabs\Network_Accounts_Tab;
use Nevamiss\Presentation\Tabs\Stats_Tab;
use Nevamiss\Presentation\Tabs\Tab_Collection;
use Nevamiss\Presentation\Tabs\Tab_Collection_Interface;
use Nevamiss\Presentation\Tabs\Tab_Interface;
use Nevamiss\Presentation\Tabs\Upgrade_Tab;
use Nevamiss\Services\Form_Validator;
use Nevamiss\Services\Network_Post_Aggregator;
use Nevamiss\Services\Network_Post_Provider;
use Nevamiss\Services\Schedule_Queue;
use Nevamiss\Services\Settings;
use Psr\Container\ContainerInterface;

class Presentation_Module implements ServiceModule, ExecutableModule {

	use ModuleClassNameIdTrait;

	public function services(): array {
		return array(

			Schedules_Page::class              => static function ( ContainerInterface $container ) {

				return new Schedules_Page(
					$container->get( Schedules_Table_List::class ),
					$container->get( Schedule_Repository::class )
				);
			},

			Settings_Page::class               => static function ( ContainerInterface $container ) {
				return new Settings_Page(
					$container->get( Settings::class ),
					$container->get( Media_Network_Collection::class ),
					$container->get( Tab_Collection::class ),
				);
			},

			Suggestions_Page::class            => static fn ( ContainerInterface $container ) => new Suggestions_Page(
				$container->get( Posts_Stats_Repository::class )
			),

			Schedule_View_Page::class          => function ( ContainerInterface $container ): Schedule_View_Page {

				return new Schedule_View_Page( $container->get( Schedule_Repository::class ) );
			},
			Schedule_Form::class               => function ( ContainerInterface $container ): Schedule_Form {

				return new Schedule_Form(
					$container->get( Schedule_Repository::class ),
					$container->get( Network_Account_Repository::class ),
					$container->get( Form_Validator::class ),
					$container->get( Factory::class )
				);
			},

			Auto_Share_Page::class             => fn ( ContainerInterface $container ): Auto_Share_Page => new Auto_Share_Page(
				$container->get( Network_Post_Aggregator::class )
			),

			Post_Meta::class                   => function ( ContainerInterface $container ) {
				return new Post_Meta(
					$container->get( Factory::class ),
					$container->get( Network_Post_Provider::class ),
					$container->get( Settings::class ),
					$container->get( Network_Account_Repository::class )
				);
			},
			Schedules_Table_List::class        => fn( ContainerInterface $container ) => new Schedules_Table_List(
				$container->get( Schedule_Repository::class ),
				$container->get( Posts_Stats_Repository::class ),
				$container->get( Schedule_Queue::class ),
				$container->get(Network_Account_Repository::class)
			),
			Logs_Table_List::class             => fn( ContainerInterface $container ) => new Logs_Table_List( $container->get( Logger_Repository::class ) ),
			Tab_Collection_Interface::class    => function ( ContainerInterface $container ) {
				$factory = $container->get( Factory::class );
				return apply_filters(
					'nevamiss-settings-tabs',
					array(
						new General_Tab( $factory, $container->get( Shortner_Collection::class ) ),
						new Network_Accounts_Tab(
							$factory,
							$container->get( Network_Accounts_Table_List::class ),
							$container->get( Media_Network_Collection::class )
						),
						new Stats_Tab( $factory, $container->get( Stats_Table_List::class ) ),
						new Logs_Tab( $factory, $container->get( Logs_Table_List::class ) ),
						new Upgrade_Tab( $factory ),
					)
				);
			},

			Tab_Collection::class              => function ( ContainerInterface $container ) {
				$collection = new Tab_Collection();
				/**
				 * @var Tab_Interface $tab
				 */
				foreach ( $container->get( Tab_Collection_Interface::class ) as $tab ) {
					$collection->register( $tab->slug(), $tab );
				}
				return $collection;
			},
			Network_Accounts_Table_List::class => fn( ContainerInterface $container ) =>
			new Network_Accounts_Table_List( $container->get( Network_Account_Repository::class ) ),
			Stats_Table_List::class            => function ( ContainerInterface $container ) {
				return new Stats_Table_List( $container->get( Posts_Stats_Repository::class ) );
			},
		);
	}

	public function run( ContainerInterface $container ): bool {
		/**
		 * @var Schedule_Form $schedule_form
		 */
		$schedule_form = $container->get( Schedule_Form::class );

		add_action(
			'admin_menu',
			static function () use ( $container, $schedule_form ) {
				$container->get( Auto_Share_Page::class )->register();
				$container->get( Schedules_Page::class )->register();
				$container->get( Settings_Page::class )->register();
				$container->get( Schedule_View_Page::class )->register();
				$container->get( Suggestions_Page::class )->register();
				$schedule_form->register();
			}
		);

		add_action(
			'add_meta_boxes',
			array( $container->get( Post_Meta::class ), 'meta_boxes' )
		);
		add_action(
			'admin_post_nevamiss_create_schedule',
			array( $schedule_form, 'maybe_save_form' )
		);

		add_action(
			'admin_post_nevamiss_settings',
			static function () use ( $container ) {

				$container->get( Settings_Page::class )->save_form();
			}
		);
		add_action(
			'admin_post_nevamiss_schedules_delete_action',
			static function () use ( $container ) {
				call_user_func( array( $container->get( Schedules_Page::class ), 'bulk_delete' ) );
			}
		);

		/**
		 * Register hook to bulk delete stats and network accounts
		 */
		add_action(
			'admin_post_delete_all',
			static function () use ( $container ) {
				call_user_func( array( $container->get( Settings_Page::class ), 'bulk_delete' ) );
			}
		);

		return true;
	}
}
