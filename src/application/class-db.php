<?php

declare(strict_types=1);

namespace Nevamiss\Application;

class DB {
    public function __construct(private $wpdb)
    {
    }

    public function setup_tables(): void
    {
        [
            'schedule' => $schedule_table_name,
            'schedule_queue' => $schedule_queue_table_name,
            'stats'   => $stats_table_name,
            'logs'   => $logs_table_name,
            'task'  => $task_table_name
        ] = $this->table_names();

        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `$schedule_table_name` (
            id INT PRIMARY KEY AUTO_INCREMENT,
            schedule_name VARCHAR(255) UNIQUE NOT NULL,
            start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            repeat_frequency ENUM NOT NULL,
            daily_times JSON,
            weekly_times JSON,
            monthly_times JSON,
            query JSON,
            accounts JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS `$schedule_queue_table_name` (
            id INT PRIMARY KEY AUTO_INCREMENT,
            schedule_id INT,
            shared_posts_ids JSON,
            all_posts_ids JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ); 
        CREATE TABLE IF NOT EXISTS `$stats_table_name` (
            id INT PRIMARY KEY AUTO_INCREMENT,
            schedule_id INT NOT NULL,
            posted_on TIMESTAMP NOT NULL CURRENT_TIMESTAMP,
            posts_ids JSON NOT NULL,
            cycles_count INT 0 CHECK,
            remote_posted ENUM(0, 1,),
            status JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        );
        CREATE TABLE IF NOT EXISTS `$task_table_name` (
            id INT PRIMARY KEY AUTO_INCREMENT,
            class_identifier VARCHAR(255) NOT NULL,
            parameter JSON,
            schedule_id INT NULL,
            status ENUM('pending', 'running', 'succeeded', 'failed'),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS `$logs_table_name` (
            id INT PRIMARY KEY AUTO_INCREMENT,
            schedule_id INT,
            messages JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ) $charset_collate;

        SQL;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta( $sql );
    }

    public function drop_tables()
    {
        [
            'schedule' => $schedule_table_name,
            'schedule_queue' => $schedule_queue_table_name,
            'stats'   => $stats_table_name,
            'logs'   => $logs_table_name,
            'task'  => $task_table_name
        ] = $this->table_names();

        $sql = <<< DROPSQL
        
            DROP TABLE IF EXISTS $stats_table_name;
            DROP TABLE IF EXISTS $schedule_table_name;
            DROP TABLE IF EXISTS $schedule_queue_table_name;
            DROP TABLE IF EXISTS $logs_table_name;
            DROP TABLE IF EXISTS $task_table_name;

        DROPSQL;

        $this->wpdb->query( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    }

    private function table_names(): array
    {
        return [
            'schedule'            => "{$this->wpdb->prefix}nevass_schedule",
            'schedule_queue'      => "{$this->wpdb->prefix}nevass_schedule_queue",
            'stats'               => "{$this->wpdb->prefix}nevass_stats",
            'task'                => "{$this->wpdb->prefix}nevass_task",
            'logs'                => "{$this->wpdb->prefix}nevass_logs",
        ];
    }
}