<?php

declare(strict_types=1);

namespace Nevamiss\Tests\Unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Nevamiss\Application\DB;
use Nevamiss\Application\Setup;
use Nevamiss\Application\Uninstall;
use Nevamiss\Services\Settings;
use Nevamiss\Services\WP_Cron_Service;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(Uninstall::class)]
class UninstallTest extends TestCase{

	use MockeryPHPUnitIntegration;

	/**
	 * @throws Exception
	 */

	public function test_it_can_deactivate()
	{
		$dbMock = $this->createMock(DB::class);
		$settingsMock = $this->createMock(Settings::class);
		$cronMock = $this->createMock(WP_Cron_Service::class);
		$cronMock->expects($this->once())->method('unschedule_all');

		$setup = new Uninstall($dbMock, $settingsMock ,$cronMock);

		$setup->deactivate();
	}


	/**
	 * @throws Exception
	 */
	public function test_it_can_cleanup()
	{
		$dbMock = $this->createMock(DB::class);
		$settingsMock = $this->createMock(Settings::class);
		$settingsMock->method('keep_records')->willReturn(false);
		$cronMock = $this->createMock(WP_Cron_Service::class);

		$dbMock->expects($this->once())->method('drop_tables');

		$setup = new Uninstall($dbMock, $settingsMock ,$cronMock);

		$setup->run();
	}
}