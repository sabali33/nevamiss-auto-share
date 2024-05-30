<?php 
namespace Nevamiss\Service;

use Saas\Inc\Domain\Repository\Schedule_Posting_Times_Repository_Interface;

class Schedule_Posting_Times_Service implements Schedule_Posting_Times_Repository_Interface {
	
	public function __construct(Schedule_Repository_Interface $schedule )
	{
		$this->schedule = $schedule;
	}
	public function get_posting_timestamps(): array
	{

	}
	public function is_time_main(): bool
	{

	}
	public function get_timestamp(string $start_date, int $start_hour, int $start_minute): int
	{
		$date = ( $start_date ) ? Saas::date( $start_date ) : Saas::date();
		$date->modify( sprintf( '+%d hour', intval( $start_hour ) ) );
		$date->modify( sprintf( '+%d minute', intval( $start_minute ) ) );
		return $date->getTimestamp();
	}
}