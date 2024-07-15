<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Services\Contracts\Date_Interface;

class Date implements Date_Interface {


	public function __construct( private \DateTime $date ) {
	}

	public function timestamp( string $date = '', string $format = 'Y-m-d' ): int {
		if ( $date ) {
			self::create_from_format( $date, $format );
		}
		return $this->date->getTimestamp();
	}

	public function posting_time_in_week( array $week_days_time ): array {
	}

	public function posting_time_in_month( array $dates ): array {
		// TODO: Implement posting_time_in_month() method.
	}

	public static function create_from_format( string $date, string $format = 'Y-m-d H:i:s' ): self {
		return new self( \DateTime::createFromFormat( $format, $date ) );
	}

	public function set_day( int $day ): void {
		$year  = (int) $this->date->format( 'Y' );
		$month = (int) $this->date->format( 'm' );
		$this->date->setDate( $year, $month, $day );
	}

	public function set_time( int $hour, int $minute ): void {
		$this->date->setTime( $hour, $minute );
	}

	public function is_late(): bool {
		return ( new \DateTime() )->getTimestamp() > $this->date->getTimestamp();
	}

	public function next_active_date( string $modifier ): void {
		$this->date->modify( $modifier );

		if ( $this->is_late() ) {
			$this->next_active_date( $modifier );
		}
	}

	public function day(): string {
		return strtolower( $this->date->format( 'l' ) );
	}

	public function next_date(): void {
		$this->date->modify( '+1 day' );
	}

	public static function timestamp_to_date( int $timestamp ): string {
		$date = ( new \DateTime() )->setTimestamp( $timestamp );
		return $date->format( 'Y-m-d H:i:s' );
	}
}
