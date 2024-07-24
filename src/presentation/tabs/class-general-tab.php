<?php
declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Components\Component_Runner;
use Nevamiss\Presentation\Components\Input_Fields\Checkbox_Group;
use Nevamiss\Presentation\Components\Input_Fields\Input;
use Nevamiss\Presentation\Components\Input_Fields\Select_Field;
use Nevamiss\Presentation\Components\Input_Fields\Select_Group_Field;
use Nevamiss\Presentation\Components\Input_Fields\TextArea;
use Nevamiss\Presentation\Components\Tabs\Section;
use Nevamiss\Presentation\Components\Tabs\Tab;
use Nevamiss\Presentation\Components\Wrapper;

class General_Tab implements Tab_Interface, Section_Interface {

	public const SLUG = 'general';
	const TEMPLATE_PATH = 'resources/templates/general-settings';
	const GENERAL_SETTINGS = 'nevamiss_general_settings';

	public function __construct( private Factory $factory ) {
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

	public function render(): string {
		ob_start();

		include NEVAMISS_PATH . self::TEMPLATE_PATH . '.php';

		return ob_get_clean();
	}

	public function sections(): array
	{
		$settings = get_option(self::GENERAL_SETTINGS);

		$general = $settings['general'] ?? [
			'repeat_cycle' => 1,
			'pause_all_schedules' => 0,
			'keep_records' => 1,
		];
		$network_api_keys = $settings['network_api_keys'] ?? [
			'networks_to_post' => ['facebook', 'x'],
			'facebook' => [ 'client_id' => '', 'client_secret' => ''],
			'x' => [ 'client_id' => '', 'client_secret' => ''],
			'linkedin' => [ 'client_id' => '', 'client_secret' => ''],
			'oa_rebrandly_api' => '',
			'oa_rebrandly_shortlink' => '',

		];

		$post = $settings['post'] ?? ['share_on_publish' => ['post']];

		return [
			'general' => array(
				'label' => __('General', 'nevamiss'),
				'fields' => array(
					array(
						'name'  => 'repeat_cycle',
						'label' => __( 'Repeat when a schedule finishes a cycle', 'nevamiss' ),
						'type'  => 'checkbox',
						'value' => $general['repeat_cycle'],
						'class' => 'repeat-cycle',
						'checked' => $general['repeat_cycle'],

					),
					array(
						'name'  => 'pause_all_schedules',
						'label' => __( 'Pause posting all schedules', 'nevamiss' ),
						'type'  => 'checkbox',
						'checked' => $general['pause_all_schedules'],
						'class' => 'network-list',
					),
					array(
						'name'  => 'keep_records',
						'label' => __( 'Keep data from this plugin on deactivation', 'nevamiss' ),
						'type'  => 'checkbox',
						'checked' => $general['keep_records'],
						'class' => 'keep-records',
					),
				)
			),
			'network_api_keys' => array(
				'label' => __('API Keys and Secrets', 'nevamiss'),
				'fields' => array(
					array(
						'name'        => 'networks_to_post[]',
						'label'       => __('Enable Facebook', 'nevamiss'),
						'type'        => 'checkbox',
						'value'        => 'facebook',
						'checked' => in_array('facebook', $network_api_keys['networks_to_post']),
						'sub_fields' => array(
							array(
								'name'        => 'facebook[client_id]',
								'label'       => __( 'App ID', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['facebook']['client_id'],
								'placeholder' => __('Enter App ID', 'Nevamiss'),
								'class'       => 'facebook-app-id',

							),
							array(
								'name'        => 'facebook[client_secret]',
								'label'       => __( 'App Secret', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['facebook']['client_secret'],
								'placeholder' => __('Enter App Secret', 'nevamiss'),
								'class'       => 'facebook-app-secret',
							),
						)
					),
					array(
						'name'        => 'networks_to_post[]',
						'label'       => __('Enable X', 'nevamiss'),
						'type'        => 'checkbox',
						'value'        => 'x',
						'checked' => in_array('x', $network_api_keys['networks_to_post']),
						'sub_fields' => array(
							array(
								'name'        => 'x[client_id]',
								'label'       => __( 'Client ID', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['x']['client_id'],
								'placeholder' => __('Enter Client ID', 'nevamiss'),
								'class'       => 'x-client-id',

							),
							array(
								'name'        => 'x[client_secret]',
								'label'       => __( 'Client Secret', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['x']['client_secret'],
								'placeholder' => __('Enter Client Secret', 'nevamiss'),
								'class'       => 'x-client-secret',
							),
						)
					),
					array(
						'name'        => 'networks_to_post[]',
						'label'       => __('Enable Linkedin', 'nevamiss'),
						'type'        => 'checkbox',
						'value'        => 'linkedin',
						'checked' => in_array('linkedin', $network_api_keys['networks_to_post']),
						'sub_fields' => array(
							array(
								'name'        => 'linkedin[client_id]',
								'label'       => __( 'Client ID', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['linkedin']['client_id'],
								'placeholder' => __('Enter Client ID', 'nevamiss'),
								'class'       => 'linkedin-client-id',

							),
							array(
								'name'        => 'linkedin[client_secret]',
								'label'       => __( 'Client Secret', 'nevamiss' ),
								'type'        => 'text',
								'value'       => $network_api_keys['linkedin']['client_secret'],
								'placeholder' => __('Enter Client Secret', 'nevamiss'),
								'class'       => 'linkedin-client-secret',
							),
						)
					),
					array(
						'name'  => 'oa_rebrandly_api',
						'label' => __( 'Rebrandly Api key', 'nevamiss' ),
						'type'  => 'text',
						'value' => $network_api_keys['oa_rebrandly_api'],
						'size'  => 30,
						'class' => 'rebrandly-api',

					),
					array(
						'name'  => 'oa_rebrandly_shortlink',
						'label' => __( 'Short Link', 'nevamiss' ),
						'type'  => 'text',
						'value' => $network_api_keys['oa_rebrandly_shortlink'],
						'size'  => 30,
						'class' => 'rebrandly-link',

					),
				)
			),
			'post' => array(
				'label' => __('Post Settings', 'nevamiss'),
				'fields' => array(
					array(
						'name'     => 'share_on_publish[]',
						'label'    => __( 'Share on publish:', 'nevamiss' ),
						'type'     => 'checkbox-group',
						'choices' => get_post_types(),
						'value'    => $post['share_on_publish'],
						'class'    => 'share-on-publish',

					),
				)
			)
		];
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function section_tabs(string $currect_section): array
	{
		$tabs_components = [];
		foreach ($this->sections() as $section_tab => $args){
			$tabs_components[] = $this->factory->component(Section::class, [
				'section' => $section_tab,
				'slug' => $this->slug(),
				'label' => $args['label'],
				'current_section' => $currect_section,
			]);
		}
		return $tabs_components;
	}

	public function redirect(array $data): void
	{
		$url = add_query_arg($data, admin_url('admin.php?page=nevamiss-settings&tab=general'));
		wp_redirect($url);
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function render_sections(string $current_section)
	{
		$sections = $this->sections();

		if(!isset($sections[$current_section]['fields'])){
			return;
		}
		$section_components = [];
		foreach( $sections[$current_section]['fields'] as $field){

			if(!isset($field['sub_fields'])){
				$section_components[] = $this->to_component($field);
				continue;
			}

			$field_component = [];
			foreach ($field['sub_fields'] as $parent_value => $sub_field){

				$field_component[] = \Nevamiss\component(
					Wrapper::class,
					[
						'attributes' => [
							'class' => "sub-field-wrap {$field['value']}"
						]
					],

					[$this->to_component($sub_field)]
				);

			}
			$section_components[] = \Nevamiss\component(
				Component_Runner::class,
				[],
				[
					$this->to_component($field),
					\Nevamiss\component(Wrapper::class, [], $field_component)
				]
			);

		}
		return $section_components;
	}

	/**
	 * @throws Not_Found_Exception
	 */
	private function to_component(mixed $field): Component
	{
		$field_class = match($field['type']){
			'select' => Select_Field::class,
			'textarea' => TextArea::class,
			'select-group' => Select_Group_Field::class,
			'checkbox-group' => Checkbox_Group::class,
			default => Input::class,
		};
		return $this->factory->component($field_class, $field);
	}

	public function maybe_save_settings(): void
	{
		if(empty($_POST)){
			return;
		}

		if(!$this->authorized()){
			wp_die('unathorized');
		}
		$data = $this->extract_data($_POST);
		$section = sanitize_text_field($_POST['section']);
		$settings = get_option(self::GENERAL_SETTINGS);

		if(!$settings){
			update_option(self::GENERAL_SETTINGS, [$section => $data]);
			return;
		}

		$settings[$section]  = $data;

		update_option(self::GENERAL_SETTINGS, $settings);

		$this->redirect([
			'status' => 'success',
			'message' => __('Settings saved!', 'nevamiss'),
			'section' => $section
		]);

		exit;
	}

	private function authorized(): bool
	{
		return isset($_POST['_wpnonce']) &&
			wp_verify_nonce($_POST['_wpnonce'], 'nevamiss-general-settings-action');
	}

	private function extract_data(array $post_data)
	{
		$fields = $this->post_keys($post_data['section']);
		return array_reduce(
			$fields,
			function($acc, $field) use($post_data){
				if(!isset($post_data[$field])){
					$acc[$field] = null;
					return $acc;
				}
				if($post_data[$field] === 'on'){
					$acc[$field] = 1;
					return $acc;
				}
				if(is_string($post_data[$field])){
					$acc[$field] = sanitize_text_field($post_data[$field]);
					return $acc;
				}
				$acc[$field] = filter_var_array($post_data[$field], FILTER_SANITIZE_ENCODED);
				return $acc;
			},
			[]
		);
	}

	private function post_keys(mixed $section): array
	{
		return match($section){
			'general' => array_column($this->sections()[$section]['fields'], 'name'),
			'network_api_keys' => [
				'networks_to_post',
				'facebook',
				'linkedin',
				'x',
				'oa_rebrandly_api',
				'oa_rebrandly_shortlink',
			],
			'post' => ['share_on_publish']
		};
	}
}
