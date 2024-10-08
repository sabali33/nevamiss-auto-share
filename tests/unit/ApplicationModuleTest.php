<?php

declare(strict_types=1);

namespace Nevamiss\Tests\Unit;


use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Nevamiss\Application\Application_Module;
use Nevamiss\Application\Assets;
use Nevamiss\Application\Uninstall;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\Functions\when;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;

#[CoversClass(Application_Module::class)]
final class ApplicationModuleTest extends TestCase {

	use MockeryPHPUnitIntegration;

	protected function setUp(): void
	{
		parent::setUp();
		setUp();
	}

	/**
	 * @throws ExpectationArgsRequired
	 * @throws Exception
	 */
	public function test_it_can_init_application()
	{
		$mockContainer = $this->createMock(
			ContainerInterface::class
		);

		$setupMock = $this->createMock(Uninstall::class);
		$assetsMock = $this->createMock(Assets::class);

		$mockContainer->expects($this->exactly(2))
			->method('get')->with($this->logicalOr(
				$this->equalTo(Uninstall::class),
				$this->equalTo(Assets::class)
			))->willReturnCallback(
				function($arg1) use($setupMock, $assetsMock) {
					return $arg1 === Uninstall::class ? $setupMock : $assetsMock;
				}
			);

		when('plugin_basename')->justReturn('nevamiss/nevamiss.php');

		define("NEVAMISS_ROOT", '/');

		$application = new Application_Module();

		expect('register_deactivation_hook')->once()->with(NEVAMISS_ROOT, [$setupMock, 'deactivate']);

		$booted = $application->run($mockContainer);

		$this->assertNotFalse(has_action('admin_enqueue_scripts', [ $assetsMock, 'enqueue_script' ]));
		$this->assertNotFalse(has_action('uninstall_nevamiss/nevamiss.php', [ $setupMock, 'run' ]));

		$this->assertTrue($booted);
	}

	protected function tearDown(): void
	{
		tearDown();
		parent::tearDown();
	}
}
