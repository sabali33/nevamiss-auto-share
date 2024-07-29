<?php

declare(strict_types=1);

namespace nevamiss\tests\unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Nevamiss\Application\Application_Module;
use Nevamiss\Application\Compatibility\Versions_Dependency_Interface;
use Nevamiss\Application\DB;
use Nevamiss\Application\Setup;
use Nevamiss\Services\Settings;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use function Brain\Monkey\Functions\expect;
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
	public static function php_versions_provider(): array
	{
		return [
			['7.0.0'],
			['7.4.0'],
			['8.0.0'],
		];
	}

	/**
	 * @throws Exception
	 */
	public function test_it_can_activate(): void
	{

		$dbMock = $this->createMock(DB::class);
		$dbMock->expects($this->once())->method('setup_tables');

		$dependencies = $this->createMock(Versions_Dependency_Interface::class);
		$dependencies->expects($this->once())->method('php_version')->willReturn('8.0');

		$setup = new Setup($dbMock, $dependencies, null);

		$activated = $setup->activate();

		$this->assertTrue($activated);
	}


	/**
	 * @throws Exception
	 */
	#[DataProvider('php_versions_provider')]
	public function test_it_can_check_versions_compatibility(string $version)
	{
		$dbMock = $this->createMock(DB::class);

		$dependencies = $this->createMock(Versions_Dependency_Interface::class);
		$dependencies->expects($this->once())->method('php_version')->willReturn($version);

		$setup = new Setup($dbMock, $dependencies, null);

		if (in_array($version, ['5.6', '7.0.0', '7.4.0'])) {
			$this->expectException("Exception");
		}

		$setup->activate();
	}

	/**
	 * @throws Exception
	 */
	#[DataProvider('can_keep_records')]
	public function test_it_can_deactivate(bool $can_keep_record)
	{

		$dbMock = $this->createMock(DB::class);


		$settingsMock = $this->createMock(Settings::class);

		$settingsMock->expects($this->once())->method('keep_records')->willReturn($can_keep_record);



		if($can_keep_record) {
			$dbMock->expects($this->never())->method('drop_tables');
		}else{
			$dbMock->expects($this->once())->method('drop_tables');
		}

		$dependencies = $this->createMock(Versions_Dependency_Interface::class);

		$setup = new Setup($dbMock, $dependencies, $settingsMock);

		$setup->deactivate();
	}

	protected function tearDown(): void
	{
		tearDown();
	}
}