<?php

declare(strict_types=1);

namespace Nevamiss\Tests\Unit\Services;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Nevamiss\Domain\Repositories\Logger_Repository;
use Nevamiss\Services\Logger;
use Nevamiss\Services\Settings;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\Functions\when;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;

#[CoversClass(Logger::class)]
class LoggerTest extends TestCase
{
	use MockeryPHPUnitIntegration;

	protected function setUp(): void
	{
		parent::setUp();
		setUp();
	}
	public static function toggle_vales()
	{
		return [
			['file'],
			['database'],
			['both']
		];
	}

	public function test_it_save_log_message_to_database()
	{
		$loggerRepositoryLogger = $this->createMock(Logger_Repository::class);
		$post_data = ['messages' => ['Test log message'], 'schedule_id' => 3];
		$settingsMock = $this->createMock(Settings::class);
		$filesystemMock = $this->createMock(\WP_Filesystem_Base::class);
		$logger = new Logger($loggerRepositoryLogger, $settingsMock, $filesystemMock);

		$loggerRepositoryLogger->expects($this->once())->method('create')->with($post_data);
		$logger->save($post_data);
	}

	public function test_it_log_message_to_file()
	{
		$loggerRepositoryLogger = $this->createMock(Logger_Repository::class);
		$post_data = ['messages' => ['Test log message'], 'schedule_id' => 3];
		$settingsMock = $this->createMock(Settings::class);
		$filesystemMock = $this->createMock(\WP_Filesystem_Base::class);

		$logger = new Logger($loggerRepositoryLogger, $settingsMock, $filesystemMock);

		expect('error_log')->once();
		expect('do_action')->with('wonolog.log.debug', [ 'message' => wp_json_encode($post_data), 'level' => 'DEBUG' ])->never();


		$logger->log_to_file($post_data);

	}

	/**
	 * @throws Exception
	 */
	#[DataProvider('toggle_vales')]
	public function test_it_log_message_from_callback(string $logType)
	{
		$loggerRepositoryLogger = $this->createMock(Logger_Repository::class);
		$post_data = ['messages' => ['Test log message', true], 'schedule_id' => 3];
		$settingsMock = $this->createMock(Settings::class);
		$settingsMock->expects($this->once())->method('logging_option')->willReturn($logType);
		$filesystemMock = $this->createMock(\WP_Filesystem_Base::class);

		$logger = new Logger($loggerRepositoryLogger, $settingsMock, $filesystemMock);

		$expect_save_data = [
			'messages' => wp_json_encode(['Test log message']),
			'schedule_id' => 3
		];

		if($logType === 'both'){
			expect('error_log')->once();
			$loggerRepositoryLogger->expects($this->once())->method('create')->with($expect_save_data)->willReturn(1);
		}elseif('file' === $logType){

			expect('error_log')->once();
			expect('do_action')->with('wonolog.log.debug', [ 'message' => wp_json_encode($post_data), 'level' => 'DEBUG' ])->never();

		}else{
			expect('error_log')->never();
			expect('do_action')->with('wonolog.log.debug', [ 'message' => wp_json_encode($post_data), 'level' => 'DEBUG' ])->never();
			$loggerRepositoryLogger->expects($this->once())->method('create')->with($expect_save_data)->willReturn(1);
		}

		$logger->log_callback($post_data['messages'], $post_data['schedule_id']);

	}

	protected function tearDown(): void
	{
		parent::tearDown();
		tearDown();
	}

}