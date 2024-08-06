<?php

declare(strict_types=1);

namespace Nevamiss\Services;

use Nevamiss\Presentation\Tabs\General_Tab;

class Settings {

	public function update( string $key, mixed $value ) {
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
		return get_option( General_Tab::GENERAL_SETTINGS );
	}

	public function network_credentials( string $network_slug ): array {

		$network_settings = $this->setting( $network_slug );
		return $network_settings ?: array();
	}

	public function enabled_networks(): array {
		$enabled_settings = $this->setting( 'networks_to_post' );
		return $enabled_settings ?: array();
	}

	public function section_settings( $section ): array|null {
		return $this->settings()[ $section ] ?? null;
	}

	public function linkedin_content_type(): string {
		$content_type = $this->setting( 'nevamiss_linkedin_content_type' );
		return $content_type ?
			$content_type :
			'article';
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
}
