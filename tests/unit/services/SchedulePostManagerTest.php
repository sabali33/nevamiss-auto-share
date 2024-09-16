<?php

namespace Nevamiss\Tests\Unit\Services;

use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Domain\Repositories\Task_Repository;
use Nevamiss\Infrastructure\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Logger;
use Nevamiss\Services\Network_Post_Manager;
use Nevamiss\Services\Network_Post_Provider;
use Nevamiss\Services\Schedule_Post_Manager;
use Nevamiss\Services\Settings;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;
use function PHPUnit\Framework\once;

#[CoversClass(Schedule_Post_Manager::class)]
class SchedulePostManagerTest extends TestCase
{
	use MockeryPHPUnitIntegration;
	protected function setUp(): void
	{
		parent::setUp();
		setUp();
	}

//	public static function test_schedule_pause_dependencies()
//	{
//		return [
//			[true, true],
//			[true, false],
//		];
//
//	}

	/**
	 * @throws Not_Found_Exception
	 * @throws Exception
	 * @throws ExpectationArgsRequired
	 */
	public function test_schedule_post_manager_can_pause_with_exception()
	{
		$mockScheduleRepository = $this->createMock(Schedule_Repository::class);

		$factoryMock = $this->createMock(Factory::class);
		$taskRepositoryMock = $this->createMock(Task_Repository::class);
		$networkPostProviderMock = $this->createMock(Network_Post_Provider::class);
		$settingsMock = $this->createMock(Settings::class);
		$settingsMock->expects($this->once())->method('pause_all_schedules')->willReturn(true);

		$scheduleManager = new Schedule_Post_Manager($mockScheduleRepository, $factoryMock, $taskRepositoryMock, $networkPostProviderMock, $settingsMock);
		$scheduleId = 3;

		expect('doing_action')->once()->with('admin_post_nevamiss_schedule_share')->andReturn(true);
		stubTranslationFunctions();

		expect('do_action')->once()->with(Logger::SCHEDULE_LOGS, ["Scheduling is paused at Settings", true], $scheduleId);

		$this->expectException('Exception');
		$scheduleManager->run(3);

	}
	public function test_schedule_post_manager_can_pause()
	{
		$mockScheduleRepository = $this->createMock(Schedule_Repository::class);

		$factoryMock = $this->createMock(Factory::class);
		$taskRepositoryMock = $this->createMock(Task_Repository::class);
		$networkPostProviderMock = $this->createMock(Network_Post_Provider::class);
		$settingsMock = $this->createMock(Settings::class);
		$settingsMock->expects($this->once())->method('pause_all_schedules')->willReturn(true);

		$scheduleManager = new Schedule_Post_Manager($mockScheduleRepository, $factoryMock, $taskRepositoryMock, $networkPostProviderMock, $settingsMock);

		expect('doing_action')->once()->with('admin_post_nevamiss_schedule_share')->andReturn(false);

		$mockScheduleRepository->expects($this->never())->method('get');
		$scheduleManager->run(3);

	}

	public function test_it_can_instantly_share_posts()
	{
		$scheduleId = 3;
		$scheduleMock = $this->createMock(Schedule::class);
		$scheduleMock->expects($this->once())->method('id')->willReturn($scheduleId);
		$networkAccountMock = $this->createMock(Network_Account::class);
		$networkClientMock = $this->createMock(Network_Clients_Interface::class);
		$dataset = [
			[
				'data' => ["Out going content"],
				'account' => $networkAccountMock,
				'network_client' => $networkClientMock
			]
		];

		$networkPostManagerMock = $this->createMock(Network_Post_Manager::class);
		$mockScheduleRepository = $this->createMock(Schedule_Repository::class);

		$factoryMock = $this->createMock(Factory::class);
		$taskRepositoryMock = $this->createMock(Task_Repository::class);
		$networkPostProviderMock = $this->createMock(Network_Post_Provider::class);
		$settingsMock = $this->createMock(Settings::class);
		$settingsMock->expects($this->once())->method('pause_all_schedules')->willReturn(false);
		$mockScheduleRepository->expects($this->once())->method('get')->with($scheduleMock->id())->willReturn($scheduleMock);
		$scheduleMock->expects($this->once())->method('is_heavy')->willReturn(false);

		$networkPostProviderMock->expects($this->once())->method('provide_instant_share_data')->with($scheduleMock)->willReturn($dataset);
		expect('do_action')->once()->with(Logger::SCHEDULE_LOGS, ["Starting to post without creating tasks"], $scheduleId);
		expect('do_action')->once()->with(Logger::SCHEDULE_LOGS, ["Post shared without creating tasks", true], $scheduleId);

		$factoryMock->expects($this->once())->method('new')->with(Network_Post_Manager::class, $networkAccountMock, $networkClientMock )->willReturn($networkPostManagerMock);
		$networkPostManagerMock->expects($this->once())->method('post')->with(["Out going content"])->willReturn('37ht2627');

		expect('do_action')->once()->with('nevamiss_schedule_network_share', '37ht2627', $scheduleId );

		expect('sleep')->once()->with(1);

		$scheduleManager = new Schedule_Post_Manager($mockScheduleRepository, $factoryMock, $taskRepositoryMock, $networkPostProviderMock, $settingsMock);

		expect('doing_action')->once()->with('admin_post_nevamiss_schedule_share')->andReturn(true);
		expect('do_action')->once()->with(Logger::SCHEDULE_LOGS, ["Preparing to share"], $scheduleId);

		$networkPostProviderMock->expects($this->never())->method('provide_for_schedule')->with($scheduleMock);

		$scheduleManager->run(3);
	}
	public function test_it_can_create_tasks()
	{
		$scheduleId = 3;
		$scheduleMock = $this->createMock(Schedule::class);
		$scheduleMock->expects($this->exactly(3))->method('id')->willReturn($scheduleId);
		$networkAccountMock = $this->createMock(Network_Account::class);
		$networkClientMock = $this->createMock(Network_Clients_Interface::class);
		$dataset = [
			[
				'data' => ["Out going content"],
				'account' => $networkAccountMock,
				'network_client' => $networkClientMock
			]
		];

		$mockScheduleRepository = $this->createMock(Schedule_Repository::class);

		$factoryMock = $this->createMock(Factory::class);
		$taskRepositoryMock = $this->createMock(Task_Repository::class);
		$networkPostProviderMock = $this->createMock(Network_Post_Provider::class);
		$settingsMock = $this->createMock(Settings::class);
		$settingsMock->expects($this->once())->method('pause_all_schedules')->willReturn(false);
		$mockScheduleRepository->expects($this->once())->method('get')->with($scheduleId)->willReturn($scheduleMock);
		$scheduleMock->expects($this->once())->method('is_heavy')->willReturn(true);

		$networkPostProviderMock->expects($this->once())->method('provide_for_schedule')->with($scheduleMock)->willReturn($dataset);
		$taskRepositoryMock->expects(once())->method('create')->with($dataset[0]);

		expect('do_action')->once()->with(Logger::SCHEDULE_LOGS, ["Starting to create tasks"], $scheduleId);
		expect('do_action')->once()->with(Logger::SCHEDULE_LOGS, ["Finished creating tasks"], $scheduleId);

		expect('do_action')->once()->with(Logger::SCHEDULE_LOGS, ["Successfully shared from tasks", true], $scheduleId);
		expect('do_action')->once()->with('nevamiss_schedule_create_tasks_completed', $scheduleId);


		$scheduleManager = new Schedule_Post_Manager($mockScheduleRepository, $factoryMock, $taskRepositoryMock, $networkPostProviderMock, $settingsMock);

		$scheduleManager->run($scheduleId);
	}

	public function test_that_run_method_can_catch_exception()
	{
		$scheduleId = 3;

		$mockScheduleRepository = $this->createMock(Schedule_Repository::class);
		$mockScheduleRepository->method('get')->with($scheduleId)->will($this->throwException(new \Exception('Example error')));

		$factoryMock = $this->createMock(Factory::class);
		$taskRepositoryMock = $this->createMock(Task_Repository::class);
		$networkPostProviderMock = $this->createMock(Network_Post_Provider::class);
		$settingsMock = $this->createMock(Settings::class);
		$settingsMock->method('pause_all_schedules')->willReturn(false);

		expect('do_action')->once()->with(Logger::SCHEDULE_LOGS, ["Preparing to share"], $scheduleId);

		expect('do_action')->with(Logger::SCHEDULE_LOGS, ['Example error', true ], $scheduleId);
		expect('doing_action')->once()->with('admin_post_nevamiss_schedule_share')->andReturn(true);

		$this->expectException('\Exception');
		$this->expectExceptionMessage('Example error');

		$scheduleManager = new Schedule_Post_Manager($mockScheduleRepository, $factoryMock, $taskRepositoryMock, $networkPostProviderMock, $settingsMock);
		$scheduleManager->run($scheduleId);


	}
	protected function tearDown(): void
	{
		parent::tearDown(); // TODO: Change the autogenerated stub
		tearDown();
	}
}