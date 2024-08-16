<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\Repositories\Logger_Repository;
use Nevamiss\Services\Contracts\Logger_Interface;

class Logger implements Logger_Interface {

	private static $messages = [];
	private static ?self $instance = null;
	public const SCHEDULE_LOGS = 'nevamiss_schedule_log';


	public function __construct(
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
	public function save(array $post_data): void {

		$this->logger_repository->create($post_data);
	}

	/**
	 * @throws \Exception
	 */
	public function log_callback(array $messages, int $schedule_id): void
	{
		self::$messages[] = $messages[0];
		$post_data = [
			'schedule_id' => $schedule_id,
			'messages' => wp_json_encode(self::$messages)
		];

		if(isset($messages[1]) && $messages[1]){

			match($this->settings->logging_option()){
				'both' => $this->log_and_save($post_data),
				'file' => $this->log_to_file($post_data),
				'database' => $this->save($post_data),
				default => false
			};
			self::$messages = [];
		}

	}

	/**
	 * @throws \Exception
	 */
	private function log_and_save(array $messages): void
	{
		$this->save($messages);
		$this->log_to_file($messages);
	}
	public function log_to_file(array $messages): void
	{
		$encoded_data = wp_json_encode($messages);

		if(class_exists(\Monolog\Logger::class)){

			do_action( 'wonolog.log.debug', [
				'message' => $encoded_data,
				'level' => 'DEBUG',
			] );
			return;
		}

		error_log($encoded_data);
	}
}
