<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\Repositories\Logger_Repository;
use Nevamiss\Services\Contracts\Logger_Interface;

class Logger implements Logger_Interface {

	public const SCHEDULE_LOGS = 'nevamiss_schedule_log';


	public function __construct(
		private Logger_Repository $logger_repository,
		private Settings $settings,
	)
	{
	}

	/**
	 * @throws \Exception
	 */
	public function save(string $message, int $schedule_id): void {
		$this->logger_repository->create(['schedule_id' => $schedule_id, 'message' => $message]);
	}

	/**
	 * @throws \Exception
	 */
	public function log_callback(string $message, int $schedule_id): void
	{
		match($this->settings->logging_option()){
			'both' => $this->log_and_save($message, $schedule_id),
			'file' => $this->log_to_file("$message, $schedule_id"),
			'database' => $this->save($message, $schedule_id),
			default => false
		};
	}

	/**
	 * @throws \Exception
	 */
	private function log_and_save(string $message, $schedule_id): void
	{
		$this->save($message, $schedule_id);
		$this->log_to_file($message);
	}
	public function log_to_file(string $message): void
	{
		if(class_exists(\Monolog\Logger::class)){
			do_action( 'wonolog.log.debug', [ 'message' => $message, 'level' => 'DEBUG' ] );
			return;
		}
		error_log($message);
	}
}
