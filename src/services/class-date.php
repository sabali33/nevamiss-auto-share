<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Exception;
use Nevamiss\Services\Contracts\Date_Interface;

class Date implements Date_Interface {


	public function __construct( private \DateTime $date ) {
	}

	/**
	 * @return \DateTimeZone|null
	 * @throws Exception
	 */
	private static function timezone(): ?\DateTimeZone {
		$timezone_string = get_option( 'timezone_string' );
		$timezone        = null;

		if ( $timezone_string ) {
			$timezone = new \DateTimeZone( $timezone_string );
		}
		return $timezone;
	}

	/**
	 * @throws Exception
	 */
	public function timestamp( string $date = '', string $format = 'Y-m-d' ): int {
		if ( $date ) {
			self::create_from_format( $date, $format );
		}
		return $this->date->getTimestamp();
	}

	/**
	 * @throws Exception
	 */
	public static function now(): Date {
		return new self( new \DateTime( 'now', self::timezone() ) );
	}

	/**
	 * @throws Exception
	 */
	public static function create_from_format( string $date, string $format = 'Y-m-d' ): self {
		return new self( \DateTime::createFromFormat( $format, $date, self::timezone() ) );
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

	public function next_day(): void {
		$this->modify( '+1 day' );
	}

	public static function timestamp_to_date( int $timestamp ): string {
		$date = ( new \DateTime() )->setTimestamp( $timestamp );
		return $date->format( 'Y-m-d H:i:s' );
	}

	public function format( string $date = null ): string {
		$date_format = $date ?? "{$this->date_format()} {$this->time_format()}";
		return $this->date->format( $date_format );
	}

	public function modify( string $modifier ): void {
		$this->date->modify( $modifier );
	}

	public function date_format(): string {
		return get_option( 'date_format' );
	}
	public function time_format(): string {
		return get_option( 'time_format' );
	}

	public function date_time(): \DateTime {
		return $this->date;
	}
	public function diff( Date $date ): \DateInterval|bool {
		return $this->date->diff( $date->date_time() );
	}

	public function add( \DateInterval $date_interval ) {
		return $this->date->add( $date_interval );
	}

	public function full_wp_date_format() {
		return "{$this->date_format()} @ {$this->time_format()}";
	}
}
