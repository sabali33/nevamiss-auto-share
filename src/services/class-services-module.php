<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Application\Post_Query\Query;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Command_Query;
use Nevamiss\Domain\Repositories\Logger_Repository;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Posts_Stats_Repository;
use Nevamiss\Domain\Repositories\Schedule_Queue_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Domain\Repositories\Task_Repository;
use Nevamiss\Infrastructure\Url_Shortner\Rebrandly;
use Nevamiss\Infrastructure\Url_Shortner\Shortner_Collection;
use Nevamiss\Networks\Network_Clients;
use Nevamiss\Presentation\Post_Meta\Post_Meta;
use Nevamiss\Services\Row_Action_Handlers\Accounts_Row_Action_Handler;
use Nevamiss\Services\Row_Action_Handlers\Schedule_Row_Action_Handler;
use Nevamiss\Services\Row_Action_Handlers\Stats_Row_Action_Handler;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Services_Module implements ServiceModule, ExecutableModule {

	use ModuleClassNameIdTrait;

	/**
	 * @throws NotFoundExceptionInterface
	 * @throws ContainerExceptionInterface
	 */
	public function run( ContainerInterface $container ): bool {
		/**
		 * @var Schedule_Post_Manager $schedule_post_manager
		 */
		$schedule_post_manager = $container->get( Schedule_Post_Manager::class );

		/**
		 * @var WP_Cron_Service $wp_cron_service
		 */
		$wp_cron_service = $container->get( WP_Cron_Service::class );

		/**
		 * @var Schedule_Row_Action_Handler $post_handler
		 */
		$post_handler = $container->get( Schedule_Row_Action_Handler::class );

		/**
		 * @var Schedule_Queue $schedule_queue
		 */
		$schedule_queue = $container->get( Schedule_Queue::class );

		/**
		 * @var Stats_Manager $stats_manager
		 */
		$stats_manager = $container->get( Stats_Manager::class );

		/**
		 * @var Schedule_Tasks_Runner $schedule_tasks_runner
		 */
		$schedule_tasks_runner = $container->get( Schedule_Tasks_Runner::class );

		/**
		 * @var Url_Shortner_Manager $url_shortner_manager
		 */
		$url_shortner_manager = $container->get( Url_Shortner_Manager::class );

		add_action( 'nevamiss_schedule_create_tasks_completed', array( $schedule_tasks_runner, 'run' ) );
		add_action( 'nevamiss_schedule_task_complete', array( $schedule_tasks_runner, 'update_task' ), 10, 2 );

		add_filter( 'cron_schedules', array( $wp_cron_service, 'add_cron' ) );
		add_action( 'nevamiss_created_schedule', array( $wp_cron_service, 'create_cron' ) );
		add_action( 'nevamiss_after_schedule_updated', array( $wp_cron_service, 'maybe_reschedule_cron' ), 10 );

		add_action( 'nevamiss_created_schedule', array( $schedule_queue, 'create_queue_callback' ) );
		add_action( 'nevamiss_after_schedule_updated', array( $schedule_queue, 'maybe_update_schedule_queue' ), 10 );
		add_action( 'nevamiss_schedule_task_complete', array( $schedule_queue, 'update_schedule_queue_callback' ), 10, 2 );

		add_action( 'nevamiss_schedule_task_complete', array( $stats_manager, 'record_stats_callback' ), 10, 2 );

		add_action( WP_Cron_Service::RECURRING_EVENT_HOOK_NAME, array( $schedule_post_manager, 'run' ) );
		add_action( WP_Cron_Service::NEVAMISS_SCHEDULE_SINGLE_EVENTS, array( $schedule_post_manager, 'run' ) );

		add_action( 'admin_post_nevamiss_schedule_delete', array( $post_handler, 'delete_schedule_callback' ) );
		add_action( 'admin_post_nevamiss_schedule_unschedule', array( $post_handler, 'unschedule_callback' ) );
		add_action( 'admin_post_nevamiss_schedule_share', array( $post_handler, 'share_schedule_posts_callback' ) );

		add_action( 'transition_post_status', array( $url_shortner_manager, 'on_post_publish' ), 10, 3 );

		add_action(
			'admin_post_nevamiss_network_accounts_delete',
			array(
				$container->get( Accounts_Row_Action_Handler::class ),
				'logout_accounts_callback',
			)
		);

		add_action(
			'admin_post_nevamiss_stats_delete',
			array(
				$container->get( Stats_Row_Action_Handler::class ),
				'delete_stat_row_callback',
			)
		);

		/**
		 * @var Ajax $ajax
		 */

		$ajax = $container->get(Ajax::class);

		add_action( 'wp_ajax_nevamiss_instant_share', array( $ajax, 'instant_posting_callback' ) );

		add_action( 'wp_ajax_nevamiss_sort_queue_posts', array( $ajax, 'sort_queue_posts_callback') );

		/**
		 * @var Logger $logger
		 */
		$logger = $container->get( Logger::class );

		add_action( 'nevamiss_schedule_log', array( $logger, 'log_callback' ), 10, 2 );

		return true;
	}

	public function services(): array {

		return array(
			Logger::class                      => fn( ContainerInterface $container ): Logger => Logger::instance(
				$container->get( Logger_Repository::class ),
				$container->get( Settings::class ),
			),
			Schedule_Post_Manager::class       => fn( ContainerInterface $container ): Schedule_Post_Manager => new Schedule_Post_Manager(
				$container->get( Schedule_Repository::class ),
				$container->get( Factory::class ),
				$container->get( Task_Repository::class ),
				$container->get( Network_Post_Provider::class ),
				$container->get( Settings::class ),
			),
			Task_Runner::class                 => fn( ContainerInterface $container ): Task_Runner => new Task_Runner(
				$container->get( Factory::class ),
				$container->get( Task_Repository::class ),
				$container->get( Network_Post_Provider::class ),
			),
			Settings::class                    => fn(): Settings => new Settings(),
			WP_Cron_Service::class             => fn( ContainerInterface $container ): WP_Cron_Service => new WP_Cron_Service(
				$container->get( Schedule_Repository::class )
			),
			Network_Post_Provider::class       => fn( ContainerInterface $container ): Network_Post_Provider => new Network_Post_Provider(
				$container->get( Settings::class ),
				$container->get( Network_Account_Repository::class ),
				$container->get( Query::class ),
				$container->get( Schedule_Queue_Repository::class ),
				$container->get( Network_Clients::class ),
				$container->get( Factory::class ),
			),
			Schedule_Tasks_Runner::class       => function ( ContainerInterface $container ) {

				return new Schedule_Tasks_Runner(
					$container->get( Task_Repository::class ),
					$container->get( Factory::class ),
					$container->get( Network_Post_Provider::class ),
				);
			},
			Form_Validator::class              => fn() => new Form_Validator(),
			Schedule_Row_Action_Handler::class => function ( ContainerInterface $container ) {
				return new Schedule_Row_Action_Handler(
					$container->get( Schedule_Repository::class ),
					$container->get( WP_Cron_Service::class ),
					$container->get( Schedule_Post_Manager::class )
				);
			},
			Accounts_Row_Action_Handler::class => function ( ContainerInterface $container ) {
				return new Accounts_Row_Action_Handler( $container->get( Network_Account_Repository::class ) );
			},
			Stats_Row_Action_Handler::class    => function ( ContainerInterface $container ) {
				return new Stats_Row_Action_Handler( $container->get( Posts_Stats_Repository::class ) );
			},
			Schedule_Queue::class              => function ( ContainerInterface $container ) {
				return new Schedule_Queue(
					$container->get( Schedule_Repository::class ),
					$container->get( Schedule_Queue_Repository::class ),
					$container->get( Query::class )
				);
			},
			Http_Request::class                => function () {
				return new Http_Request();
			},
			Accounts_Manager::class            => function ( ContainerInterface $container ) {
				return new Accounts_Manager( $container->get( Network_Account_Repository::class ) );
			},
			Stats_Manager::class               => fn( ContainerInterface $container ) => new Stats_Manager( $container->get( Posts_Stats_Repository::class ) ),
			Ajax::class                        => fn( ContainerInterface $container ) => new Ajax( $container->get( Post_Meta::class ), $container->get(Schedule_Queue_Repository::class) ),
			Url_Shortner_Manager::class        => fn( ContainerInterface $container ) => new Url_Shortner_Manager(
				$container->get( Settings::class ),
				$container->get( Shortner_Collection::class )
			),
			Network_Post_Aggregator::class => function (ContainerInterface $container) {
				return new Network_Post_Aggregator(
					$container->get(Schedule_Repository::class),
					$container->get(Schedule_Queue_Repository::class),
					$container->get(WP_Cron_Service::class),
					$container->get(Command_Query::class),
					$container->get(Query::class),
				);
			}
		);
	}
}
