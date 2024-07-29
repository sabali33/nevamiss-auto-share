<?php

declare(strict_types=1);

namespace nevamiss\tests\unit;


use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Nevamiss\Application\Application_Module;
use Nevamiss\Application\Assets;
use Nevamiss\Application\Setup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\tearDown;

#[CoversClass(Application_Module::class)]
final class ApplicationModuleTest extends TestCase {

	protected function setUp(): void
	{
		parent::setUp();
		\Brain\Monkey\setUp();
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

		$setupMock = $this->createMock(Setup::class);
		$assetsMock = $this->createMock(Assets::class);

		$mockContainer->expects($this->exactly(2))
			->method('get')->with($this->logicalOr(
				$this->equalTo(Setup::class),
				$this->equalTo(Assets::class)
			))->willReturnCallback(
				function($arg1) use($setupMock, $assetsMock) {
					return $arg1 === Setup::class ? $setupMock : $assetsMock;
				}
			);


		\Mockery::spy('register_activation_hook');
		define("NEVAMISS_ROOT", '/');

		$application = new Application_Module();
		expect('register_deactivation_hook')->once()->with(NEVAMISS_ROOT, [$setupMock, 'deactivate']);

		$booted = $application->run($mockContainer);

		$this->assertNotFalse(has_action('admin_enqueue_scripts', [ $assetsMock, 'enqueue_script' ]));

		$this->assertTrue($booted);
	}
	protected function tearDown(): void
	{
		tearDown();
	}
}
