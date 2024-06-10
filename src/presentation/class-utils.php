<?php

declare(strict_types=1);

namespace Nevamiss\Presentation;

class Utils {
	public static function build_input_attr( array $attributes ) {
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
            function( $item ) use ( $callback ) {
                if ( $callback ) {
                    return $callback( $item );
                }
                return ! ! $item;
            }
        );
        return count( $checked ) === count( $data );
    }
}
