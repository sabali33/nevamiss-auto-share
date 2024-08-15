<?php

namespace Nevamiss\Tests\Unit\Services;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Services\WP_Cron_Service;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;

#[CoversClass(WP_Cron_Service::class)]
class WPCronServiceTest extends TestCase
{
	use MockeryPHPUnitIntegration;
	protected function setUp(): void
	{
		parent::setUp();
		setUp();

		$scheduleRepository = $this->createMock(Schedule_Repository::class);
		$this->cronService = new WP_Cron_Service($scheduleRepository);
	}

	public function test_it_can_init_cron_service()
	{
		self::assertInstanceOf(WP_Cron_Service::class, $this->cronService);
	}

	public static function frequency_types()
	{
		return [
			[
				'none', // frequency
				['2024-07-12 3:04', '2024-10-12 3:04'], // dates
			    'one_time_schedule', //date_function
			    null, // start date
				'wp_schedule_single_event', //wp schedule function
			],

			[
				'daily',
				[
					[
						'hour' => 15,
						'minute' => 25
					],
					[
						'hour' => 12,
						'minute' => 45
					]
				],
				'daily_times',
				'2024-08-15',
				'wp_schedule_event',
			],
			[
				'weekly',
				[
					[
						'hour' => 15,
						'minute' => 25,
						'day' => 'monday',
					],
					[
						'hour' => 12,
						'minute' => 45,
						'day' => 'tuesday'
					]
				],
				'weekly_times',
				'2024-08-15',
				'wp_schedule_event',
			],
			[
				'monthly',
				[
					[
						'hour' => 15,
						'minute' => 25,
						'day' => 12,
					],
					[
						'hour' => 12,
						'minute' => 45,
						'day' => 23
					]
				],
				'monthly_times',
				'2024-08-15',
				'wp_schedule_event',
			],

		];
	}

	#[DataProvider('frequency_types')]
	public function test_it_can_create_cron(...$schedule_data)
	{
		[
			$frequency,
			$dates,
			$date_function,
			$start_date,
			$wp_cron_event_func,
		] = $schedule_data;
		$scheduleMock = $this->createMock(Schedule::class);

		$scheduleMock->method($date_function)->willReturn($dates);
		$scheduleMock->method('id')->willReturn(2);
		if($frequency !== 'none'){
			$scheduleMock->method('one_time_schedule')->willReturn(null);
		}
		$scheduleMock->method('repeat_frequency')->willReturn($frequency);
		$scheduleMock->method('start_date')->willReturn($start_date);

		\Mockery::spy($wp_cron_event_func);
		\Mockery::spy('Nevamiss\Services\get_option');
		expect($wp_cron_event_func)->times(count($dates))->andReturn(true);
		expect('get_option')->times(count($dates));

		$scheduleRepository = $this->createMock(Schedule_Repository::class);
		$scheduleRepository->expects($this->once())->method('get')->with($scheduleMock->id())->willReturn($scheduleMock);
		$cronService = new WP_Cron_Service($scheduleRepository);

		$this->assertTrue($cronService->create_cron(2));
	}

	public function test_it_can_create_daily_cron_can_throw_exception()
	{
		$dates = [
			[
				'hour' => 15,
				'minute' => 25
			],
			[
				'hour' => 12,
				'minute' => 45
			]
		];
		$scheduleMock = $this->createMock(Schedule::class);
		$scheduleMock->method('one_time_schedule')->willReturn(null);
		$scheduleMock->method('daily_times')->willReturn($dates);
		$scheduleMock->method('repeat_frequency')->willReturn('daily');
		$scheduleMock->method('start_date')->willReturn('2024-08-15');
		$scheduleMock->method('id')->willReturn(3);

		\Mockery::spy('wp_schedule_event');
		\Mockery::spy('Nevamiss\Services\get_option');
		expect('wp_schedule_event')->once()->andReturn(false);
		expect('get_option')->times(count($dates));
		stubEscapeFunctions();

		$scheduleRepository = $this->createMock(Schedule_Repository::class);
		$scheduleRepository->expects($this->once())->method('get')->willReturn($scheduleMock);
		$cronService = new WP_Cron_Service($scheduleRepository);
		$this->expectException('\Exception');
		$cronService->create_cron(3);

	}

	public function test_it_can_create_one_time_schedule_cron_can_throw_exception()
	{
		$dates = ['2024-07-12 3:04', '2024-10-12 3:04'];
		$scheduleMock = $this->createMock(Schedule::class);
		$scheduleMock->method('one_time_schedule')->willReturn($dates);
		$scheduleMock->method('id')->willReturn(3);

		\Mockery::spy('wp_schedule_single_event');
		\Mockery::spy('Nevamiss\Services\get_option');
		expect('wp_schedule_single_event')->once()->andReturn(false);
		expect('get_option')->times(count($dates));
		stubEscapeFunctions();

		$scheduleRepository = $this->createMock(Schedule_Repository::class);
		$scheduleRepository->expects($this->once())->method('get')->willReturn($scheduleMock);
		$cronService = new WP_Cron_Service($scheduleRepository);
		$this->expectException('\Exception');
		$cronService->create_cron(3);

	}

	public function test_it_can_reschedule_cron()
	{
		$scheduleMock = $this->createMock(Schedule::class);
		$scheduleMock->method('one_time_schedule')->willReturn(['2024-07-12 3:04', '2024-10-12 3:04']);
		$scheduleMock->method('repeat_frequency')->willReturn('daily');
		$scheduleMock->method('id')->willReturn(3);
		$newScheduleMock = $this->createMock(Schedule::class);
		$newScheduleMock->method('one_time_schedule')->willReturn(['2024-07-13 3:04', '2024-10-14 3:04']);
		$newScheduleMock->method('id')->willReturn(3);

		$scheduleRepository = $this->createMock(Schedule_Repository::class);
		$scheduleRepository->expects($this->once())->method('get')->willReturn($newScheduleMock);
		$cronService = new WP_Cron_Service($scheduleRepository);
		expect('wp_clear_scheduled_hook')->once()->
		with(WP_Cron_Service::RECURRING_EVENT_HOOK_NAME, [ $scheduleMock->id()])->
		andReturn(2);
		expect('get_option')->times(2);
		expect('wp_schedule_single_event')->times(2)->andReturn(true);
		$cronService->maybe_reschedule_cron($scheduleMock);
	}

	protected function tearDown(): void
	{
		tearDown();
		parent::tearDown();
	}
}