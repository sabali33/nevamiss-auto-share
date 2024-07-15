<?php

declare(strict_types=1);

namespace Nevamiss\Application;

class DB {
	public function __construct( private $wpdb ) {
	}

	public function setup_tables(): void {
		[
			'schedule' => $schedule_table_name,
			'schedule_queue' => $schedule_queue_table_name,
			'stats'   => $stats_table_name,
			'logs'   => $logs_table_name,
			'task'  => $task_table_name,
			'network_account'  => $network_account_table_name
		] = $this->table_names();

		$charset_collate = $this->wpdb->get_charset_collate();

		$sql = "CREATE TABLE $schedule_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            schedule_name VARCHAR(255) UNIQUE NOT NULL,
            start_date TIMESTAMP,
            repeat_frequency ENUM('none', 'daily', 'weekly', 'monthly') NOT NULL DEFAULT 'none',
            social_media_tags VARCHAR(255),
            one_time_schedule JSON,
            daily_times JSON,
            weekly_times JSON,
            monthly_times JSON,
            query_args JSON NOT NULL,
            network_accounts JSON NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;
        CREATE TABLE $schedule_queue_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            schedule_id INT,
            cycles INT DEFAULT 0 CHECK (cycles >= 0),
            shared_posts_ids JSON,
            all_posts_ids JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;
        CREATE TABLE $stats_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            schedule_id INT NOT NULL,
            posted_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
            post_id INT NOT NULL,
            remote_post_id VARCHAR(60) NOT NULL,
            remote_posted ENUM('0', '1') DEFAULT '0',
            PRIMARY KEY (id)
        ) $charset_collate;
        CREATE TABLE $task_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            class_identifier VARCHAR(255) NOT NULL,
            parameters JSON,
            schedule_id INT NULL,
            status ENUM('pending', 'running', 'succeeded', 'failed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;
        CREATE TABLE $logs_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            schedule_id INT,
            messages JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;
        CREATE TABLE $network_account_table_name (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(50),
            network VARCHAR(50),
            remote_account_id VARCHAR(60),
            parent_remote_id VARCHAR(60),
            token TEXT DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $sql );
	}

	public function drop_tables(): void {
		[
			'schedule' => $schedule_table_name,
			'schedule_queue' => $schedule_queue_table_name,
			'stats'   => $stats_table_name,
			'logs'   => $logs_table_name,
			'task'  => $task_table_name,
			'network_account'     => $network_account_table_name
		] = $this->table_names();

		$sql = "DROP TABLE IF EXISTS 
            `$stats_table_name`, 
            `$schedule_table_name`, 
            `$schedule_queue_table_name`,
            `$logs_table_name`,
            `$task_table_name`,
            `$network_account_table_name`";

		$this->wpdb->query( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}

	private function table_names(): array {
		return array(
			'schedule'        => "{$this->wpdb->prefix}nevamiss_schedules",
			'schedule_queue'  => "{$this->wpdb->prefix}nevamiss_schedule_queue",
			'stats'           => "{$this->wpdb->prefix}nevamiss_stats",
			'task'            => "{$this->wpdb->prefix}nevamiss_tasks",
			'logs'            => "{$this->wpdb->prefix}nevamiss_logs",
			'network_account' => "{$this->wpdb->prefix}nevamiss_network_accounts",
		);
	}
}
