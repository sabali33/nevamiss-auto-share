<?php

declare(strict_types=1);

namespace Nevamiss\Tests\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Nevamiss\Application\DB;
use Nevamiss\Application\Setup;
use Nevamiss\Services\WP_Cron_Service;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\tearDown;

#[CoversClass(Setup::class)]
final class SetupTest extends TestCase
{
	use MockeryPHPUnitIntegration;

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

	protected function tearDown(): void
	{
		tearDown();
		parent::tearDown();
	}
}