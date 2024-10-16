<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Domain\Repositories\Logger_Repository;
use Nevamiss\Services\Contracts\Logger_Interface;
use WP_Filesystem_Base;

class Logger implements Logger_Interface {

	const FS_PERM_MODE = 0644;
	private static $messages       = array();
	private static ?self $instance = null;
	public const SCHEDULE_LOGS     = 'nevamiss_schedule_log';
	public const GENERAL_LOGS      = 'nevamiss_general_log';


	public function __construct(
		private Logger_Repository $logger_repository,
		private Settings $settings,
		private WP_Filesystem_Base $wp_filesystem,
		private string $log_file
	) {
	}

	public static function instance(
		Logger_Repository $logger_repository,
		Settings $settings,
		WP_Filesystem_Base $filesystem,
		string $log_file = 'nevamiss-log.txt'
	): Logger {
		if ( self::$instance ) {
			return self::$instance;
		}
		self::$instance = new self( $logger_repository, $settings, $filesystem, $log_file );
		return self::$instance;
	}

	/**
	 * @throws \Exception
	 */
	public function save( array $post_data ): void {

		$this->logger_repository->create( $post_data );
	}

	/**
	 * @throws \Exception
	 */
	public function log_callback( array $messages, int $schedule_id ): void {
		self::$messages[] = $messages[0];
		$post_data        = array(
			'schedule_id' => $schedule_id,
			'messages'    => wp_json_encode( self::$messages ),
		);

		if ( isset( $messages[1] ) && $messages[1] ) {

			match ( $this->settings->logging_option() ) {
				'both' => $this->log_and_save( $post_data ),
				'file' => $this->log_to_file( wp_json_encode($post_data )),
				'database' => $this->save( $post_data ),
				default => false
			};
			self::$messages = array();
		}
	}

	/**
	 * @throws \Exception
	 */
	private function log_and_save( array $messages ): void {
		$this->save( $messages );

		$this->log_to_file( wp_json_encode( $messages ) );
	}
	public function log_to_file( string $messages, ?string $file = null ): void {

		$upload_dir = wp_upload_dir();
		$file = $file ?? $this->log_file;
		$log_file   = trailingslashit( $upload_dir['basedir'] ) . $file;

		$time      = current_time( 'mysql' );
		$formatted_message = sprintf( "[%s] %s\n", $time, $messages );

		if ( $this->wp_filesystem->exists( $log_file ) ) {
			$already_logged = $this->wp_filesystem->get_contents($log_file);
			$new_log_message = $already_logged . "\n" . $formatted_message;
			$this->wp_filesystem->put_contents( $log_file, $new_log_message, self::FS_PERM_MODE);
		} else {
			$this->wp_filesystem->put_contents( $log_file, $formatted_message, self::FS_PERM_MODE);
		}
	}
}
