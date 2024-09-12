<?php

declare(strict_types=1);

namespace Nevamiss\Tests\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Nevamiss\Application\Application_Module;
use Nevamiss\Application\Compatibility\Versions_Dependency_Interface;
use Nevamiss\Application\DB;
use Nevamiss\Application\Setup;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Services\Settings;
use Nevamiss\Services\WP_Cron_Service;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\Functions\stubs;
use function Brain\Monkey\Functions\when;
use function Brain\Monkey\tearDown;

#[CoversClass(Setup::class)]
final class SetupTest extends TestCase
{
	use MockeryPHPUnitIntegration;

	public static function can_keep_records(): array
	{
		return [
			[true],
			[true]
		];
	}

	protected function setUp(): void
	{
		parent::setUp();
		\Brain\Monkey\setUp();
	}

	/**
	 * @throws Exception
	 * @throws \Exception
	 */
	public function test_it_can_activate(): void
	{
		$dbMock = $this->createMock(DB::class);
		$dbMock->expects($this->once())->method('setup_tables');

		$cronMock = $this->createMock(WP_Cron_Service::class);

		expect('version_compare')->once()->andReturn(false);

		$reflection = new ReflectionClass(Setup::class);
		$constructor = $reflection->getConstructor();
		$constructor->setAccessible(false);

		$setup = $reflection->newInstanceWithoutConstructor();
		$constructor->invoke($setup, $dbMock, $cronMock);


		$activateMethod = $reflection->getMethod('activate');

		$activated = $activateMethod->invoke($setup);

		$this->assertTrue($activated);
	}


	/**
	 * @throws Exception
	 * @throws \ReflectionException
	 */

	public function test_it_can_check_versions_compatibility()
	{
		$dbMock = $this->createMock(DB::class);

		expect('version_compare')->once()->andReturn(true);
		$cronMock = $this->createMock(WP_Cron_Service::class);

		$reflection = new ReflectionClass(Setup::class);
		$constructor = $reflection->getConstructor();
		$constructor->setAccessible(false);

		$setup = $reflection->newInstanceWithoutConstructor();
		$constructor->invoke($setup, $dbMock, $cronMock);


		$activateMethod = $reflection->getMethod('activate');

		$this->expectException("Exception");

		$activateMethod->invoke($setup);
	}

	/**
	 * @throws Exception
	 */
//	#[DataProvider('can_keep_records')]
//	public function test_it_can_deactivate(bool $can_keep_record)
//	{
//		$dbMock = $this->createMock(DB::class);
//		$dbMock->expects($this->once())->method('setup_tables');
//
//		$cronMock = $this->createMock(WP_Cron_Service::class);
//
//		$setup = Setup::instance($dbMock, $cronMock);
//
//		$setup->deactivate();
//	}

	protected function tearDown(): void
	{
		tearDown();
		parent::tearDown();
	}
}