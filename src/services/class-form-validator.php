<?php

declare(strict_types=1);

namespace Nevamiss\Services;

class Form_Validator {

	private array $errors = array();

	public function validate_string( string $field, string $value, int $min_length = 1, int $max_length = 255 ): bool {
		$value = trim( $value );
		if ( strlen( $value ) < $min_length || strlen( $value ) > $max_length ) {
			$this->errors[ $field ] = "The $field must be between $min_length and $max_length characters.";
			return false;
		}
		return true;
	}

	public function validate_date( string $field, string $value, string $format = 'Y-m-d' ): bool {
		$d = \DateTime::createFromFormat( $format, $value );
		if ( $d && $d->format( $format ) === $value ) {
			return true;
		} else {
			$this->errors[ $field ] = "The $field is not a valid date.";
			return false;
		}
	}

	public function validate_number( string $field, $value, $min = null, $max = null ): bool {
		if ( ! is_numeric( $value ) ) {
			$this->errors[ $field ] = "The $field must be a number.";
			return false;
		}
		if ( null !== $min && $value < $min ) {
			$this->errors[ $field ] = "The $field must be at least $min.";
			return false;
		}
		if ( null !== $max && $value > $max ) {
			$this->errors[ $field ] = "The $field must be no more than $max.";
			return false;
		}
		return true;
	}

	public function validate_assoc_array_of_numbers( string $field, array $value ): bool {
		foreach ( $value as $key => $sub_array ) {
			if ( ! is_array( $sub_array ) ) {
				$this->errors[ "$field.$key" ] = "The $field at $key must be an array.";
				return false;
			}
			foreach ( $sub_array as $sub_key => $val ) {
				if ( ! is_numeric( $val ) ) {
					$this->errors[ "$field.$key.$sub_key" ] = "The $field at $key.$sub_key must be a number.";
					return false;
				}
			}
		}
		return true;
	}

	public function sanitize_string( string $value ): string {
		return sanitize_text_field( trim( $value ) );
	}

	public function sanitize_date( string $value, string $format = 'Y-m-d' ): string {
		$date = \DateTime::createFromFormat( $format, $value );
		return $date ? $date->format( $format ) : '';
	}

	public function sanitize_number( $value ): float|int
	{
		return absint($value);
	}

	public function sanitize_assoc_array_of_numbers( array $value ): array {
		$sanitize_arr = array();
		foreach ( $value as $key => &$sub_array ) {
			$sanitize_sub_arr = array();
			foreach ( $sub_array as $sub_key => &$val ) {
				$sanitize_sub_arr[ $sub_key ] = $this->sanitize_number( $val );
			}
			$sanitize_arr[ $key ] = $sanitize_sub_arr;
		}
		return $sanitize_arr;
	}

	public function sanitize_array_of_string( array $data ): array {
		return filter_var_array( $data, FILTER_SANITIZE_ENCODED );
	}

	public function errors(): array {
		return $this->errors;
	}
	public function add_error( string $error ): bool {
		$this->errors[] = $error;
		return true;
	}
}
