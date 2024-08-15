<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\Repositories\Logger_Repository;
use Nevamiss\Services\Contracts\Logger_Interface;

class Logger implements Logger_Interface {

	private static $messages = [];
	private static ?self $instance = null;
	public const SCHEDULE_LOGS = 'nevamiss_schedule_log';


	private function __construct(
		private Logger_Repository $logger_repository,
		private Settings $settings,
	)
	{
	}

	public static function instance( Logger_Repository $logger_repository, Settings $settings,): Logger
	{
		if(self::$instance){
			return self::$instance;
		}
		self::$instance = new self($logger_repository, $settings);
		return self::$instance;
	}

	/**
	 * @throws \Exception
	 */
	public function save(array $messages, int $schedule_id): void {
		$this->logger_repository->create([
			'schedule_id' => $schedule_id,
			'messages' => wp_json_encode($messages)
		]);
	}

	/**
	 * @throws \Exception
	 */
	public function log_callback(array $messages, int $schedule_id): void
	{
		self::$messages[] = $messages[0];

		if(isset($messages[1]) && $messages[1]){
			match($this->settings->logging_option()){
				'both' => $this->log_and_save(self::$messages, $schedule_id),
				'file' => $this->log_to_file(self::$messages, $schedule_id),
				'database' => $this->save(self::$messages, $schedule_id),
				default => false
			};
			self::$messages = [];
		}

	}

	/**
	 * @throws \Exception
	 */
	private function log_and_save(array $messages, $schedule_id): void
	{
		$this->save($messages, $schedule_id);
		$this->log_to_file($messages, $schedule_id);
	}
	public function log_to_file(array $messages, int $schedule_id): void
	{
		$messages[] = "Schedule ID $schedule_id";

		if(class_exists(\Monolog\Logger::class)){

			do_action( 'wonolog.log.debug', [
				'message' => wp_json_encode($messages),
				'level' => 'DEBUG',
			] );
			//return;
		}
		$message_string = join('\n', $messages);
		error_log($message_string);
	}
}
