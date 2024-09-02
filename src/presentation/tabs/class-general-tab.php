<?php
declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Infrastructure\Url_Shortner\Shortner_Collection;
use Nevamiss\Infrastructure\Url_Shortner\URL_Shortner_Interface;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Components\Input_Fields\Checkbox_Group;
use Nevamiss\Presentation\Components\Input_Fields\Input;
use Nevamiss\Presentation\Components\Input_Fields\Select_Field;
use Nevamiss\Presentation\Components\Input_Fields\Select_Group_Field;
use Nevamiss\Presentation\Components\Input_Fields\TextArea;
use Nevamiss\Presentation\Components\Tabs\Section;
use Nevamiss\Presentation\Components\Tabs\Tab;
use Nevamiss\Presentation\Components\Wrapper;
use Nevamiss\Presentation\Pages\Settings_Page;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class General_Tab implements Tab_Interface, Section_Interface {

	use Render_Interface;

	public const SLUG   = 'general';
	const TEMPLATE_PATH = 'resources/templates/general-settings';

	public function __construct( private Factory $factory, private Shortner_Collection $collection ) {
	}

	public function label(): string {
		return __( 'General', 'nevamiss' );
	}

	public function slug(): string {
		return self::SLUG;
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function link( string $active_tab ): Component {
		return $this->factory->component(
			Tab::class,
			array(
				'slug'       => $this->slug(),
				'label'      => $this->label(),
				'active_tab' => $active_tab,
			)
		);
	}

	public function sections(): array {
		$settings = get_option( Settings_Page::GENERAL_SETTINGS );

		$general = wp_parse_args(
			$settings['general'] ?? array(),
			array(
				'repeat_cycle'        => 1,
				'pause_all_schedules' => 0,
				'keep_records'        => 1,
				'logging_option'      => 'database',
			)
		);

		$network_api_keys = wp_parse_args( $settings['network_api_keys'] ?? array(), $this->default_values() );

		$post = $settings['post'] ?? array( 'share_on_publish' => array( 'post' ) );

		$fields = array(
			'general'          => array(
				'label'  => __( 'General', 'nevamiss' ),
				'fields' => array(
					array(
						'name'    => 'repeat_cycle',
						'label'   => __( 'Repeat when a schedule finishes a cycle', 'nevamiss' ),
						'type'    => 'checkbox',
						'value'   => $general['repeat_cycle'],
						'class'   => 'repeat-cycle',
						'checked' => $general['repeat_cycle'],

					),
					array(
						'name'    => 'pause_all_schedules',
						'label'   => __( 'Pause posting all schedules', 'nevamiss' ),
						'type'    => 'checkbox',
						'checked' => $general['pause_all_schedules'],
						'class'   => 'network-list',
					),
					array(
						'name'    => 'keep_records',
						'label'   => __( 'Keep data from this plugin on deactivation', 'nevamiss' ),
						'type'    => 'checkbox',
						'checked' => $general['keep_records'],
						'class'   => 'keep-records',
					),
					array(
						'name'    => 'logging_option',
						'label'   => __( 'Log to:', 'nevamiss' ),
						'type'    => 'select',
						'class'   => 'logging-option',
						'value'   => array( $general['logging_option'] ),
						'choices' => array(
							'file'     => __( 'File', 'nevamiss' ),
							'database' => __( 'Database', 'nevamiss' ),
							'both'     => __( 'Both file and database', 'nevamiss' ),
						),
					),
				),
			),
			'network_api_keys' => array(
				'label'  => __( 'API Keys and Secrets', 'nevamiss' ),
				'fields' => array(
					array(
						'name'       => 'networks_to_post[]',
						'label'      => __( 'Enable Facebook', 'nevamiss' ),
						'type'       => 'checkbox',
						'value'      => 'facebook',
						'checked'    => in_array( 'facebook', $network_api_keys['networks_to_post'] ),

						'sub_fields' => array(
							array(
								'name'        => 'facebook[client_id]',
								'label'       => __( 'App ID', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['facebook']['client_id'] ?? '',
								'placeholder' => __( 'Enter App ID', 'Nevamiss' ),
								'class'       => 'facebook-app-id',
								'disabled'    => ! in_array( 'facebook', $network_api_keys['networks_to_post'] ),

							),
							array(
								'name'        => 'facebook[client_secret]',
								'label'       => __( 'App Secret', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['facebook']['client_secret'] ?? '',
								'placeholder' => __( 'Enter App Secret', 'nevamiss' ),
								'class'       => 'facebook-app-secret',
								'disabled'    => ! in_array( 'facebook', $network_api_keys['networks_to_post'] ),
							),
							array(
								'name'        => 'facebook[app_configuration]',
								'label'       => __( 'App Configuration', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['facebook']['app_configuration'] ?? '',
								'placeholder' => __( 'Enter the App Configuration', 'nevamiss' ),
								'class'       => 'facebook-app-configurat',
								'disabled'    => ! in_array( 'facebook', $network_api_keys['networks_to_post'] ),
							),
						),
					),
					array(
						'name'       => 'networks_to_post[]',
						'label'      => __( 'Enable X', 'nevamiss' ),
						'type'       => 'checkbox',
						'value'      => 'x',
						'checked'    => in_array( 'x', $network_api_keys['networks_to_post'] ),
						'sub_fields' => array(
							array(
								'name'        => 'x[client_id]',
								'label'       => __( 'Client ID', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['x']['client_id'] ?? '',
								'placeholder' => __( 'Enter Client ID', 'nevamiss' ),
								'class'       => 'x-client-id',
								'disabled'    => ! in_array( 'x', $network_api_keys['networks_to_post'] ),

							),
							array(
								'name'        => 'x[client_secret]',
								'label'       => __( 'Client Secret', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['x']['client_secret'] ?? '',
								'placeholder' => __( 'Enter Client Secret', 'nevamiss' ),
								'class'       => 'x-client-secret',
								'disabled'    => ! in_array( 'x', $network_api_keys['networks_to_post'] ),
							),
						),
					),
					array(
						'name'       => 'networks_to_post[]',
						'label'      => __( 'Enable Linkedin', 'nevamiss' ),
						'type'       => 'checkbox',
						'value'      => 'linkedin',
						'checked'    => in_array( 'linkedin', $network_api_keys['networks_to_post'] ),
						'sub_fields' => array(
							array(
								'name'        => 'linkedin[client_id]',
								'label'       => __( 'Client ID', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['linkedin']['client_id'] ?? '',
								'placeholder' => __( 'Enter Client ID', 'nevamiss' ),
								'class'       => 'linkedin-client-id',
								'disabled'    => ! in_array( 'linkedin', $network_api_keys['networks_to_post'] ),

							),
							array(
								'name'        => 'linkedin[client_secret]',
								'label'       => __( 'Client Secret', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['linkedin']['client_secret'] ?? '',
								'placeholder' => __( 'Enter Client Secret', 'nevamiss' ),
								'class'       => 'linkedin-client-secret',
								'disabled'    => ! in_array( 'linkedin', $network_api_keys['networks_to_post'] ),
							),
						),
					),
					$this->url_shortner_fields(
						array(
							'url_shortner_client' => $network_api_keys['url_shortner_client'],
							'rebrandly'           => array(
								'api_key'   => $network_api_keys['rebrandly']['api_key'] ?? '',
								'shortlink' => $network_api_keys['rebrandly']['shortlink'] ?? '',
							),
						)
					),
				),
			),
			'post'             => array(
				'label'  => __( 'Post Settings', 'nevamiss' ),
				'fields' => array(
					array(
						'name'    => 'share_on_publish[]',
						'label'   => __( 'Share on publish:', 'nevamiss' ),
						'type'    => 'checkbox-group',
						'choices' => get_post_types(),
						'value'   => $post['share_on_publish'],
						'class'   => 'share-on-publish',

					),
				),
			),
		);
		return apply_filters( 'nevamiss-settings-fields', $fields, $settings );
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function section_tabs( string $currect_section ): array {
		$tabs_components = array();
		foreach ( $this->sections() as $section_tab => $args ) {
			$tabs_components[] = $this->factory->component(
				Section::class,
				array(
					'section'         => $section_tab,
					'slug'            => $this->slug(),
					'label'           => $args['label'],
					'current_section' => $currect_section,
				)
			);
		}
		return $tabs_components;
	}

	public function url_shortner_fields( array $values ): array {
		$fields = array();
		foreach ( $this->collection->all() as  $client ) {
			$fields = array_merge( $fields, $client->settings_fields( $values ) );
		}
		return $fields;
	}

	/**
	 * @throws Not_Found_Exception
	 * @throws NotFoundExceptionInterface
	 * @throws ContainerExceptionInterface
	 * @throws \Throwable
	 */
	public function render_sections( string $current_section ): array {
		$sections = $this->sections();

		if ( ! isset( $sections[ $current_section ]['fields'] ) ) {
			$current_section = self::SLUG;
		}
		$section_components = array();
		foreach ( $sections[ $current_section ]['fields'] as $field ) {

			if ( ! isset( $field['sub_fields'] ) ) {
				$section_components[] = $this->to_component( $field );
				continue;
			}

			$field_component = array();
			foreach ( $field['sub_fields'] as $sub_field ) {

				$field_component[] = $this->to_component( $sub_field );

			}

			$sub_field_components_wrap = \Nevamiss\component(
				Wrapper::class,
				array(
					'attributes' => array(
						'class' => "sub-field-wrapper {$field['value']}",
					),
				),
				array(
					\Nevamiss\component( Wrapper::class, array(), $field_component ),
				)
			);
			$section_components[]      = \Nevamiss\component(
				Wrapper::class,
				array(
					'attributes' => array(
						'class' => 'field-wrap',
					),
				),
				array(
					$this->to_component( $field ),
					$sub_field_components_wrap,
				)
			);
		}
		return $section_components;
	}

	/**
	 * @throws Not_Found_Exception
	 */
	private function to_component( mixed $field ): Component {
		$field_class = match ( $field['type'] ) {
			'select' => Select_Field::class,
			'textarea' => TextArea::class,
			'select-group' => Select_Group_Field::class,
			'checkbox-group' => Checkbox_Group::class,
			default => Input::class,
		};
		return $this->factory->component( $field_class, $field );
	}

	/**
	 * @return array
	 */
	private function default_values(): array {
		return apply_filters(
			'nevamiss-default-settings-values',
			array(
				'networks_to_post'    => array( 'facebook', 'x' ),
				'facebook'            => array(
					'client_id'     => '',
					'client_secret' => '',
				),
				'x'                   => array(
					'client_id'     => '',
					'client_secret' => '',
				),
				'linkedin'            => array(
					'client_id'     => '',
					'client_secret' => '',
				),
				'rebrandly'           => array(
					'api_key'   => '',
					'shortlink' => '',
				),
				'url_shortner_client' => '',
			)
		);
	}
}
