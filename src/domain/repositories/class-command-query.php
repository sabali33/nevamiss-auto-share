<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

class Command_Query {
	public function __construct(private \wpdb $wpdb)
	{
	}

	public function last_posted(): array
	{
		$stats_table_name = "{$this->wpdb->prefix}nevamiss_stats";
		$schedule_table_name = "{$this->wpdb->prefix}nevamiss_schedules";

		$sql = "
				SELECT 
				    schedule.id AS schedule_id,
				    schedule.schedule_name,
				    stats.posted_on,
				    stats.post_id,
				    stats.remote_post_id 
				FROM 
			        $schedule_table_name AS schedule
	            INNER JOIN 
			        $stats_table_name AS stats 
		        ON 
				    schedule.id = stats.schedule_id 
				ORDER BY 
			    	stats.posted_on DESC LIMIT 5";

		return $this->wpdb->get_results($sql, ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}
}