<?php

declare(strict_types=1);

namespace Nevamiss\Presentation;

class Utils {
	public static function build_input_attr( array $attributes ): string {
		$output = '';
		foreach ( $attributes as $attr => $value ) {
			if ( ! $value ) {
				continue;
			}
			$output .= empty( $output ) ? "$attr='$value'" : " $attr='$value'";
		}

		return $output;
	}
	public static function every( array $data, callable $callback = null ): bool {
		$checked = array_filter(
			$data,
			function ( $item ) use ( $callback ) {
				if ( $callback ) {
					return $callback( $item );
				}
				return (bool) $item;
			}
		);
		return count( $checked ) === count( $data );
	}
	public static function is_leap_year( int $year ): bool {
		if ( $year % 4 !== 0 ) {
			return false;
		}
		if ( $year % 100 !== 0 ) {
			return true;
		}

		if ( $year % 400 == 0 ) {
			return true;
		}
		return false;
	}
}
