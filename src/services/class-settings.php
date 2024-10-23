<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Infrastructure\Url_Shortner\Rebrandly;
use Nevamiss\Presentation\Pages\Settings_Page;

class Settings {

	public function update( string $key, mixed $value, $section = 'general' ): void {
		$settings = $this->settings();
		if ( ! isset( $settings[ $section ] ) ) {
			$settings[ $settings ] = array( $key => $value );
		}
		$settings[ $section ][ $key ] = $value;

		update_option( Settings_Page::GENERAL_SETTINGS, $settings );
	}

	public function setting( string $key, string $section = null ): mixed {

		$settings = $this->settings();

		if ( ! $settings ) {
			return false;
		}
		if ( $section ) {
			return $this->section_settings( $section )[ $key ];
		}

		foreach ( $settings as $setting_section ) {
			if ( isset( $setting_section[ $key ] ) ) {
				return $setting_section[ $key ];
			}
		}
		return '';
	}
	public function settings() {
		return get_option( Settings_Page::GENERAL_SETTINGS );
	}

	public function network_credentials( string $network_slug ): array {

		$network_settings = $this->setting( $network_slug );
		return $network_settings ? $network_settings : array();
	}

	public function enabled_networks(): array {
		$enabled_settings = $this->setting( 'networks_to_post' );
		return $enabled_settings ? $enabled_settings : array();
	}

	public function section_settings( $section ): array|null {
		return $this->settings()[ $section ] ?? null;
	}

	public function linkedin_content_type(): string {
		$content_type = $this->setting( 'nevamiss_linkedin_content_type' );
		return $content_type ? $content_type : 'article';
	}

	public function repeat_cycle() {
		return $this->setting( 'repeat_cycle' );
	}

	public function pause_all_schedules() {
		return $this->setting( 'pause_all_schedules' );
	}

	public function keep_records() {
		return $this->setting( 'keep_records' );
	}

	public function share_on_publish() {
		return $this->setting( 'share_on_publish' );
	}

	public function allowed_post_types() {
		return $this->share_on_publish();
	}

	public function logging_option() {

		return $this->setting( 'logging_option' );
	}

	public function url_shortner(): string {
		$shortner = $this->setting( 'url_shortner_client' );
		return $shortner ? $shortner : 'rebrandly';
	}
	public function url_shortner_credentials( string $id = Rebrandly::REBRANDLY ): array {
		$url_shortner_client = $this->url_shortner();
		return $this->setting( $url_shortner_client );
	}

	public function cleanup(): void {
		foreach ( array(
			'nevamiss-x-refresh-token',
			'nevamiss-linkedin-refresh-token',
			Settings_Page::GENERAL_SETTINGS,
		) as $key ) {
			delete_option( $key );
		}
	}
}
