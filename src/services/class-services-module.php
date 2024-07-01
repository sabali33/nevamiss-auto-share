<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Application\Post_Query\Query;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Schedule_Queue_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Domain\Repositories\Task_Repository;
use Nevamiss\Networks\Network_Clients;
use Psr\Container\ContainerInterface;

class Services_Module implements ServiceModule, ExecutableModule {

	use ModuleClassNameIdTrait;

	public function run( ContainerInterface $container ): bool {
		/**
		 * @var Schedule_Post_Manager $schedule_post_manager
		 */
		$schedule_post_manager = $container->get(Schedule_Post_Manager::class);

		/**
		 * @var WP_Cron_Service $wp_cron_service
		 */
		$wp_cron_service = $container->get(WP_Cron_Service::class);

		/**
		 * @var Post_Handler $post_handler
		 */
		$post_handler = $container->get(Post_Handler::class);

		/**
		 * @var Schedule_Queue $schedule_queue
		 */
		$schedule_queue = $container->get(Schedule_Queue::class);

		add_action( 'schedule_create_tasks_completed', array( $container->get( Schedule_Tasks_Runner::class ), 'run' ) );
		add_action( 'nevamiss_schedule_task_complete', array( $container->get( Schedule_Tasks_Runner::class ), 'update_task' ) );

		add_action('cron_schedules', array($wp_cron_service, 'add_cron'));
		add_action( 'nevamiss_created_schedule', array( $wp_cron_service , 'create_cron'));
		add_action('nevamiss_after_schedule_updated', array($wp_cron_service, 'maybe_reschedule_cron'), 10);

		add_action( 'nevamiss_created_schedule', array( $schedule_queue, 'create_queue_callback'));
		add_action('nevamiss_after_schedule_updated', array($schedule_queue, 'maybe_update_schedule_queue'), 10);
		add_action('nevamiss_schedule_task_complete', array($schedule_queue, 'update_schedule_queue_callback'), 10, 2);

		add_action( WP_Cron_Service::RECURRING_EVENT_HOOK_NAME, array( $schedule_post_manager, 'run' ) );
		add_action( WP_Cron_Service::NEVAMISS_SCHEDULE_SINGLE_EVENTS, array( $schedule_post_manager, 'run' ) );

		add_action('admin_post_nevamiss_schedule_delete', [$post_handler, 'delete_schedule_callback']);
		add_action('admin_post_nevamiss_schedule_unschedule', [$post_handler, 'unschedule_callback']);
		add_action('admin_post_nevamiss_schedule_share', [$post_handler, 'share_schedule_posts_callback']);

		return true;
	}

	public function services(): array {
		return array(
			Logger::class                => fn(): Logger => new Logger(),
			Schedule_Post_Manager::class => fn( ContainerInterface $container ): Schedule_Post_Manager => new Schedule_Post_Manager(
				$container->get( Schedule_Repository::class ),
				$container->get( Factory::class ),
				$container->get( Task_Repository::class ),
				$container->get( Network_Post_Provider::class ),
				$container->get( Query::class )
			),
			Task_Runner::class           => fn( ContainerInterface $container ): Task_Runner => new Task_Runner(
				$container->get( Factory::class ),
				$container->get( Task_Repository::class ),
				$container->get( Network_Post_Provider::class ),
			),
			Settings::class              => fn(): Settings => new Settings(),
			WP_Cron_Service::class       => fn(ContainerInterface $container): WP_Cron_Service => new WP_Cron_Service(
				$container->get(Schedule_Repository::class)
			),
			Network_Post_Provider::class => fn( ContainerInterface $container ): Network_Post_Provider => new Network_Post_Provider(
				$container->get( Settings::class ),
				$container->get( Network_Account_Repository::class ),
				$container->get( Query::class ),
				$container->get(Schedule_Queue_Repository::class),
				$container->get( Network_Clients::class )
			),
			Schedule_Tasks_Runner::class => function ( ContainerInterface $container ) {

				return new Schedule_Tasks_Runner(
					$container->get( Task_Repository::class ),
					$container->get( Factory::class ),
					$container->get( Network_Post_Provider::class ),
				);
			},
			Form_Validator::class => fn() => new Form_Validator(),
			Post_Handler::class => function (ContainerInterface $container) {
				return new Post_Handler(
					$container->get(Schedule_Repository::class),
					$container->get(WP_Cron_Service::class),
					$container->get(Schedule_Post_Manager::class)
				);
			},
			Schedule_Queue::class => function(ContainerInterface $container){
				return new Schedule_Queue(
					$container->get(Schedule_Repository::class),
					$container->get(Schedule_Queue_Repository::class),
					$container->get(Query::class)
				);
			}
		);
	}
}
