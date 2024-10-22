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
		if ( 0 !== $year % 4 ) {
			return false;
		}
		if ( 0 !== $year % 100 ) {
			return true;
		}

		if ( 0 === $year % 400 ) {
			return true;
		}
		return false;
	}
}
