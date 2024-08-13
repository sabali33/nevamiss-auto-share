<?php

declare(strict_types=1);

namespace unit\services;

use Nevamiss\Services\Ajax;
use Nevamiss\Services\Logger;
use Nevamiss\Services\Row_Action_Handlers\Accounts_Row_Action_Handler;
use Nevamiss\Services\Row_Action_Handlers\Schedule_Row_Action_Handler;
use Nevamiss\Services\Row_Action_Handlers\Stats_Row_Action_Handler;
use Nevamiss\Services\Schedule_Post_Manager;
use Nevamiss\Services\Schedule_Queue;
use Nevamiss\Services\Schedule_Tasks_Runner;
use Nevamiss\Services\Services_Module;
use Nevamiss\Services\Stats_Manager;
use Nevamiss\Services\WP_Cron_Service;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;

#[CoversClass(Services_Module::class)]
class ServicesModuleTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();
		setUp();
	}
	public function test_it_can_init_services()
	{
		$mockContainer = $this->createMock(ContainerInterface::class);

		$schedulePostManagerMock = $this->createMock(Schedule_Post_Manager::class);
		$wpCronServiceMock = $this->createMock(WP_Cron_Service::class);
		$scheduleRowMock = $this->createMock(Schedule_Row_Action_Handler::class);
		$scheduleQueueMock = $this->createMock(Schedule_Queue::class);
		$statsManagerMock = $this->createMock(Stats_Manager::class);
		$scheduleTaskRunnerMock = $this->createMock(Schedule_Tasks_Runner::class);
		$accountsRowActionMock = $this->createMock(Accounts_Row_Action_Handler::class);
		$statsRowActionMock = $this->createMock(Stats_Row_Action_Handler::class);
		$ajaxMock = $this->createMock(Ajax::class);
		$loggerMock = $this->createMock(Logger::class);

		$mockContainer->expects($this->exactly(10))
			->method('get')->with($this->logicalOr(
				$this->equalTo(Schedule_Post_Manager::class),
				$this->equalTo(WP_Cron_Service::class),
				$this->equalTo(Schedule_Row_Action_Handler::class),
				$this->equalTo(Schedule_Queue::class),
				$this->equalTo(Stats_Manager::class),
				$this->equalTo(Schedule_Tasks_Runner::class),
				$this->equalTo(Accounts_Row_Action_Handler::class),
				$this->equalTo(Stats_Row_Action_Handler::class),
				$this->equalTo(Ajax::class),
				$this->equalTo(Logger::class),
			))->willReturnCallback(
				function($arg1) use(
					$schedulePostManagerMock,
					$wpCronServiceMock,
					$scheduleRowMock,
					$scheduleQueueMock,
					$statsManagerMock,
					$statsRowActionMock,
					$ajaxMock,
					$scheduleTaskRunnerMock,
					$accountsRowActionMock,
					$loggerMock
				) {
					return match ($arg1) {
						Schedule_Post_Manager::class => $schedulePostManagerMock,
						WP_Cron_Service::class => $wpCronServiceMock,
						Schedule_Row_Action_Handler::class => $scheduleRowMock,
						Schedule_Queue::class => $scheduleQueueMock,
						Stats_Manager::class => $statsManagerMock,
						Stats_Row_Action_Handler::class => $statsRowActionMock,
						Ajax::class => $ajaxMock,
						Schedule_Tasks_Runner::class => $scheduleTaskRunnerMock,
						Accounts_Row_Action_Handler::class => $accountsRowActionMock,
						Logger::class => $loggerMock,
						default => '',
					};
				}
			);
		$servicesModule = new Services_Module();
		$booted = $servicesModule->run($mockContainer);
		$this->assertNotFalse(has_action('schedule_create_tasks_completed', [ $scheduleTaskRunnerMock, 'run' ]));
		$this->assertNotFalse(has_action('nevamiss_schedule_task_complete', [ $scheduleTaskRunnerMock, 'update_task' ]));

		$this->assertNotFalse(has_filter('cron_schedules', [ $wpCronServiceMock, 'add_cron' ]));
		$this->assertNotFalse(has_action('nevamiss_created_schedule', [ $wpCronServiceMock, 'create_cron' ]));
		$this->assertNotFalse(has_action('nevamiss_after_schedule_updated', [ $wpCronServiceMock, 'maybe_reschedule_cron' ]));

		$this->assertNotFalse(has_action('nevamiss_created_schedule', [ $scheduleQueueMock, 'create_queue_callback' ]));
		$this->assertNotFalse(has_action('nevamiss_after_schedule_updated', [ $scheduleQueueMock, 'maybe_update_schedule_queue' ]));
		$this->assertNotFalse(has_action('nevamiss_schedule_task_complete', [ $scheduleQueueMock, 'update_schedule_queue_callback' ]));

		$this->assertNotFalse(has_action('nevamiss_schedule_task_complete', [ $statsManagerMock, 'record_stats_callback' ]));

		$this->assertNotFalse(has_action(WP_Cron_Service::RECURRING_EVENT_HOOK_NAME, [ $schedulePostManagerMock, 'run' ]));
		$this->assertNotFalse(has_action(WP_Cron_Service::NEVAMISS_SCHEDULE_SINGLE_EVENTS, [ $schedulePostManagerMock, 'run' ]));

		$this->assertNotFalse(has_action('admin_post_nevamiss_schedule_delete', [ $scheduleRowMock, 'delete_schedule_callback' ]));
		$this->assertNotFalse(has_action('admin_post_nevamiss_schedule_unschedule', [ $scheduleRowMock, 'unschedule_callback' ]));
		$this->assertNotFalse(has_action('admin_post_nevamiss_schedule_share', [ $scheduleRowMock, 'share_schedule_posts_callback' ]));

		$this->assertNotFalse(has_action('admin_post_nevamiss_network_accounts_delete', [ $accountsRowActionMock, 'logout_accounts_callback' ]));

		$this->assertNotFalse(has_action('admin_post_nevamiss_stats_delete', [ $statsRowActionMock, 'delete_stat_row_callback' ]));

		$this->assertNotFalse(has_action('wp_ajax_nevamiss_instant_share', [ $ajaxMock, 'instant_posting_callback' ]));

		$this->assertNotFalse(has_action(Logger::SCHEDULE_LOGS, [$loggerMock, 'log_callback']));

		$this->assertTrue($booted);
	}
	protected function tearDown(): void
	{
		tearDown();
	}
}