<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Infrastructure\networks\Media_Network_Collection;
use Nevamiss\Presentation\Tabs\Bulk_Delete_Interface;
use Nevamiss\Presentation\Tabs\General_Tab;
use Nevamiss\Presentation\Tabs\Tab_Collection;
use Nevamiss\Presentation\Tabs\Tab_Interface;
use Nevamiss\Services\Settings;
use function Nevamiss\sanitize_text_input_field;

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

	public function render_tab( string $tab ): ?Tab_Interface {
		return $this->tab( $tab ) ?? $this->tab( General_Tab::SLUG );
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

		if ( $this->tab_collection->tab_exists( $tab ) ) {
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

		$data = $this->sanitize_data();

		if ( ! $data ) {
			$this->redirect(
				array(
					'status'  => 'error',
					'message' => __( 'No Section set', 'nevamiss' ),
					'section' => 'general',
				)
			);
			exit;
		}
		$section  = sanitize_text_input_field( 'section', 'post' );
		$settings = get_option( self::GENERAL_SETTINGS );

		if ( ! $settings ) {
			update_option( self::GENERAL_SETTINGS, array( $section => $data ) );
			$this->redirect(
				$this->success_message( $section )
			);
			exit;
		}

		$settings[ $section ] = $data;

		update_option( self::GENERAL_SETTINGS, $settings );

		$this->redirect(
			$this->success_message( $section )
		);

		exit;
	}
	private function authorized(): bool {
		$nonce = sanitize_text_input_field( '_wpnonce', 'post' );

		return (bool) wp_verify_nonce( $nonce, 'nevamiss-general-settings-action' );
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
				'logging_option'      => $string_type,
			),
			'network_api_keys' => array(
				'networks_to_post'    => $array_type,
				'facebook'            => $array_type,
				'linkedin'            => $array_type,
				'x'                   => $array_type,
				'instagram'           => $array_type,
				'rebrandly'           => $array_type,
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
		wp_safe_redirect( $url );
	}

	public function bulk_delete() {
		$model_name = sanitize_text_input_field( 'model_name' );

		/**
		 * @var Tab_Interface & Bulk_Delete_Interface $tab
		 */
		$tab = $this->tab_collection->get( $model_name );

		$tab->bulk_delete( $model_name );
	}

	/**
	 * @return array|false
	 */
	private function sanitize_data(): array|false {
		if ( ! isset( $_POST['section'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return false;
		}
		$schema = apply_filters(
			'nevamiss_settings_schema',
			$this->post_keys( sanitize_text_field( wp_unslash( $_POST['section'] ) ) ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
		);

		$data = array();
		foreach ( $schema as $key => $value ) {
			['type' => $type] = $value;

			if ( ! isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$data[ $key ] = $this->translate_data_type( $type );
				continue;
			}
			if ( 'on' === $_POST[ $key ] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$data[ $key ] = 1;
				continue;
			}
			if ( is_string( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$data[ $key ] = sanitize_text_field( wp_unslash( $_POST[ $key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
				continue;
			}

			$data[ $key ] = map_deep(
				wp_unslash( $_POST[ $key ] ), // phpcs:ignore WordPress.Security.NonceVerification.Missing
				'sanitize_text_field',
			);

		}
		return $data;
	}

	/**
	 * @param string|null $section
	 * @return array
	 */
	private function success_message( ?string $section ): array {
		return array(
			'status'  => 'success',
			'message' => __( 'Settings saved!', 'nevamiss' ),
			'section' => $section,
		);
	}
}
