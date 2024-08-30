<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Networks\Media_Network_Collection;
use Nevamiss\Presentation\Tabs\General_Tab;
use Nevamiss\Presentation\Tabs\Tab_Collection;
use Nevamiss\Presentation\Tabs\Tab_Interface;
use Nevamiss\Services\Settings;

class Settings_Page extends Page {
	public const TEMPLE_PATH = 'templates/settings';
	const SLUG               = 'nevamiss-settings';
	const GENERAL_SETTINGS   = 'nevamiss_general_settings';
	private Media_Network_Collection $network_collection;

	public function __construct(
		private Settings $settings,
		private Media_Network_Collection $collection,
		private Tab_Collection $tab_collection,
	) {

		$this->network_collection = $collection;

		parent::__construct(
			$settings,
			'Settings',
			self::SLUG,
			10,
			Auto_Share_Page::SLUG,
			true
		);
	}

	public function network_collection(): Media_Network_Collection {
		return $this->network_collection;
	}

	public function render_tab(string $tab): ?Tab_Interface
	{
		return $this->tab($tab) ?? $this->tab(General_Tab::SLUG);
	}

	public function settings(): Settings {
		return $this->data;
	}

	/**
	 * @return Array<Tab_Interface>
	 */
	public function tabs(): array {
		return $this->tab_collection->get_all();
	}

	public function tab( string $tab ): ?Tab_Interface {

		if($this->tab_collection->tab_exists($tab)){
			return $this->tab_collection->get( $tab );
		}
		return null;
	}

	public function save_form(): void {
		if ( empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		if ( ! $this->authorized() ) {
			wp_die( 'unathorized' );
		}

		$data     = $this->extract_data( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$section  = sanitize_text_field( $_POST['section'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$settings = get_option( self::GENERAL_SETTINGS );

		if ( ! $settings ) {
			update_option( self::GENERAL_SETTINGS, array( $section => $data ) );
			return;
		}

		$settings[ $section ] = $data;

		update_option( self::GENERAL_SETTINGS, $settings );

		$this->redirect(
			array(
				'status'  => 'success',
				'message' => __( 'Settings saved!', 'nevamiss' ),
				'section' => $section,
			)
		);

		exit;
	}
	private function authorized(): bool {
		return isset( $_POST['_wpnonce'] ) &&
			wp_verify_nonce( $_POST['_wpnonce'], 'nevamiss-general-settings-action' );
	}
	private function extract_data( array $post_data ): array {
		$schema        = apply_filters(
			'nevamiss-settings-schema',
			$this->post_keys( $post_data['section'] )
		);
		$sanitize_data = array();

		foreach ( $schema as $key => $value ) {
			['type' => $type ] = $value;

			if ( ! isset( $post_data[ $key ] ) ) {
				$sanitize_data[ $key ] = $this->translate_data_type( $type );
				continue;
			}
			if ( $post_data[ $key ] === 'on' ) {
				$sanitize_data[ $key ] = 1;
				continue;
			}
			if ( is_string( $post_data[ $key ] ) ) {
				$sanitize_data[ $key ] = sanitize_text_field( $post_data[ $key ] );
				continue;
			}
			$sanitize_data[ $key ] = filter_var_array(
				$post_data[ $key ],
				FILTER_SANITIZE_ENCODED
			);

		}

		return $sanitize_data;
	}

	private function post_keys( mixed $section ): array {
		$array_type   = array( 'type' => 'array' );
		$string_type  = array( 'type' => 'string' );
		$boolean_type = array( 'type' => 'boolean' );

		return match ( $section ) {
			'general' => array(
				'repeat_cycle'        => $boolean_type,
				'pause_all_schedules' => $boolean_type,
				'keep_records'        => $boolean_type,
				'logging_option'        => $string_type,
			),
			'network_api_keys' => array(
				'networks_to_post'       => $array_type,
				'facebook'               => $array_type,
				'linkedin'               => $array_type,
				'x'                      => $array_type,
				'oa_rebrandly_api'       => $string_type,
				'oa_rebrandly_shortlink' => $string_type,
				'rebrandly' => $array_type,
				'url_shortner_client' => $string_type,
			),
			'post' => array( 'share_on_publish' => $array_type )
		};
	}

	private function translate_data_type( mixed $type ): array|int|string {
		return match ( $type ) {
			'array' => array(),
			'string' => '',
			'boolean' => 0,
		};
	}
	public function redirect( array $data ): void {
		$url = add_query_arg( $data, admin_url( 'admin.php?page=nevamiss-settings&tab=general' ) );
		wp_redirect( $url );
	}
}
