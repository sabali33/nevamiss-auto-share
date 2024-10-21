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
use function Brain\Monkey\Functions\stubs;
use function Brain\Monkey\Functions\when;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;

#[CoversClass(Logger::class)]
class LoggerTest extends TestCase
{
	use MockeryPHPUnitIntegration;

	public static function logFileExist()
	{
		return [
			[true],
			[false]
		];
	}

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
		$logger = new Logger($loggerRepositoryLogger, $settingsMock, $filesystemMock, NEVAMISS_PATH . 'tests/log.txt');

		$loggerRepositoryLogger->expects($this->once())->method('create')->with($post_data);
		$logger->save($post_data);
	}

	#[DataProvider('logFileExist')]
	public function test_it_log_message_to_file(bool $logFileExist)
	{
		$loggerRepositoryLogger = $this->createMock(Logger_Repository::class);
		$post_data = json_encode(['messages' => ['Test log message'], 'schedule_id' => 3]);
		$settingsMock = $this->createMock(Settings::class);
		$filesystemMock = $this->createMock(\WP_Filesystem_Base::class);
		$filesystemMock->method('exists')->willReturn($logFileExist);
		stubs([
			'trailingslashit' => ''
		]);
		when('wp_upload_dir')->justReturn([
			'basedir' => ''
		]);
		when('current_time')->justReturn((new \DateTime())->getTimestamp());
		$test_log_file = NEVAMISS_PATH . 'tests/log.txt';

		$assertFunc = match($logFileExist){
			true => function() use($filesystemMock, $post_data, $test_log_file){
				$filesystemMock->expects($this->once())->method('get_contents')->with($test_log_file)->willReturn('Previous log message');

				$expected_post_data = 'Previous log message' . "\n" . json_encode([(new \DateTime())->getTimestamp()]) ." " . $post_data . "\n";
				$filesystemMock->expects($this->once())->method('put_contents')->with($test_log_file, $expected_post_data, 0644);
			},
			false => function() use($filesystemMock, $post_data, $test_log_file){
				$expected_post_data = json_encode([(new \DateTime())->getTimestamp()]) ." " . $post_data . "\n";
				$filesystemMock->expects($this->once())->method('put_contents')->with($test_log_file, $expected_post_data, 0644);

			}
		};

		$logger = new Logger($loggerRepositoryLogger, $settingsMock, $filesystemMock, $test_log_file);

		$assertFunc();

		$logger->log_to_file($post_data);

	}

	/**
	 * @throws Exception
	 * @throws \Exception
	 */
	#[DataProvider('toggle_vales')]
	public function test_it_log_message_from_callback(string $logType)
	{
		$loggerRepositoryLogger = $this->createMock(Logger_Repository::class);
		$post_data = ['messages' => ['Test log message', true], 'schedule_id' => 3];
		$settingsMock = $this->createMock(Settings::class);
		$settingsMock->expects($this->once())->method('logging_option')->willReturn($logType);
		$filesystemMock = $this->createMock(\WP_Filesystem_Base::class);
		stubs([
			'trailingslashit' => ''
		]);
		when('wp_upload_dir')->justReturn([
			'basedir' => ''
		]);
		when('current_time')->justReturn((new \DateTime())->getTimestamp());

		$logger = new Logger($loggerRepositoryLogger, $settingsMock, $filesystemMock, NEVAMISS_PATH . 'tests/log.txt');

		$expect_save_data = [
			'messages' => json_encode(['Test log message']),
			'schedule_id' => 3
		];

		if($logType === 'both'){
			$test_log_file = NEVAMISS_PATH . 'tests/log.txt';
			$expected_post_data = json_encode([(new \DateTime())->getTimestamp()]) ." " . json_encode(['schedule_id' => 3, 'messages' => json_encode(['Test log message']) ]) . "\n";

			$filesystemMock->expects($this->once())->method('put_contents')->with($test_log_file, $expected_post_data, 0644);
			$loggerRepositoryLogger->expects($this->once())->method('create')->with($expect_save_data)->willReturn(1);

		}elseif('file' === $logType){
			$test_log_file = NEVAMISS_PATH . 'tests/log.txt';
			$expected_post_data = json_encode([(new \DateTime())->getTimestamp()]) ." " . json_encode(['schedule_id' => 3, 'messages' => json_encode(['Test log message']) ]) . "\n";

			$filesystemMock->expects($this->once())->method('put_contents')->with($test_log_file, $expected_post_data, 0644);

		}else{

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