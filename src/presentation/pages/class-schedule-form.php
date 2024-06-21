<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Presentation\Components\Input_Fields\Input;
use Nevamiss\Presentation\Components\Input_Fields\Select_Field;
use Nevamiss\Presentation\Components\Input_Fields\Select_Group_Field;
use Nevamiss\Presentation\Components\Input_Fields\TextArea;
use Nevamiss\Presentation\Utils;
use Nevamiss\Services\Form_Validator;

class Schedule_Form extends Page {

	public const TEMPLE_PATH = 'templates/schedule-form';
	public const SLUG        = 'edit-schedule';

	public function __construct(
		private Schedule_Repository $schedule_repository,
		private Network_Account_Repository $account_repository,
		private Form_Validator $validator,
		private Factory $factory
	) {
		$title = isset( $_REQUEST['schedule_id'] ) ?
			__( 'Edit Schedule', 'nevamiss' ) :
			__( 'New Schedule', 'nevamiss' );

		parent::__construct(
			$schedule_repository,
			$title,
			self::SLUG,
			10,
			null,
			true
		);
	}

	public function factory(): Factory {
		return $this->factory;
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function render_field( array $field ): void {

		$field_class = match ($field['type']) {
			'select' => Select_Field::class,
			'textarea' => TextArea::class,
			'select-group' => Select_Group_Field::class,
			default => Input::class,
		};

		echo $this->factory()->component( $field_class, $field )->render();

		if ( isset( $field['sub_fields'] ) ) {
			foreach ( $field['sub_fields'] as $key => $sub_field ) {
				if ( ! empty( $sub_field ) ) {
					$selected_class = $field['value'] === $key ? ' active' : '';

					if(str_contains($key, '!')) {

						if("!{$field['value']}" !== $key){
							$selected_class = 'active';
						}
					}

					$parent_value = esc_attr($key);
					echo "<div class='sub-field-wrapper{$selected_class} $key' data-repeat-frequency='{$parent_value}'>";
						$this->render_field( $sub_field );
						echo $sub_field['has_multiple'] ? __('<button class="add-field-group button"> Add </button>', 'nevamiss') : '';
					echo '</div>';
				}
			}
		}
	}

	public function fields(): array {

		return array(
			array(

				'label' => __( 'Name', 'nevamiss' ),
				'name'  => 'schedule_name',
				'value' => '',
				'class' => 'schedule-name',
				'type'  => 'text',

			),
			array(
				'name'       => 'repeat_frequency',
				'value'      => 'none',
				'class'      => 'repeat-frequency',
				'id'         => 'repeat-frequency',
				'type'       => 'select',
				'label'      => __( 'Repeat Frequency', 'nevamiss' ),
				'choices'    => array(
					'none'    => __( 'None', 'nevamiss' ),
					'daily'   => __( 'Daily', 'nevamiss' ),
					'weekly'  => __( 'Weekly', 'nevamiss' ),
					'monthly' => __( 'Monthly', 'nevamiss' ),
				),
				'sub_fields' => array(
					'!none' => array(
						'name'  => 'start_date',
						'value' => '',
						'class' => 'start-date date',
						'type'  => 'date',
						'label' => __( 'Start Date', 'nevamiss' ),
						'has_multiple' => false,
					),
					'none'    => array(
						'name' => 'one_time_schedule[]',
						'type' => 'date',
						'class' => 'datetime date-time',
						'label' => __('Select Date'),
						'value' => '',
						'has_multiple' => true,
					),
					'daily'   => array(
						'name'              => 'daily_times[hours][]',
						'value'             => array(),
						'type'              => 'select-group',
						'class'             => 'daily-times',
						'label'             => __( 'Hour', 'nevamiss' ),
						'choices'           => range( 0, 23 ),
						'has_multiple'      => true,
						'complement_fields' => array(
							array(
								'name'    => 'daily_times[minutes][]',
								'value'   => array(),
								'type'    => 'select',
								'class'   => 'daily-times-minute',
								'label'   => __( 'Minute', 'nevamiss' ),
								'choices' => range( 1, 60, 5 ),
								'id'      => 'daily-minute'
							),
						),
					),
					'weekly'  => array(
						'name'              => 'weekly_times[days][]',
						'value'             => '',
						'type'              => 'select-group',
						'class'             => 'weekly-times',
						'id'                => 'weekly-times',
						'label'             => __( 'Weekly Times', 'nevamiss' ),
						'has_multiple'      => true,
						'choices'           => array(
							'monday'    => __( 'Monday', 'nevamiss' ),
							'tuesday'   => __( 'Tuesday', 'nevamiss' ),
							'wednesday' => __( 'Wednesday', 'nevamiss' ),
							'thursday'  => __( 'Thursday', 'nevamiss' ),
							'friday'    => __( 'Friday', 'nevamiss' ),
							'saturday'  => __( 'Saturday', 'nevamiss' ),
							'sunday'    => __( 'Sunday', 'nevamiss' ),
						),
						'complement_fields' => array(
							array(
								'name'    => 'weekly_times[hours][]',
								'value'   => array(),
								'type'    => 'select',
								'class'   => 'weekly-daily-hour',
								'id'   => 'weekly-daily-hour',
								'label'   => __( 'at', 'nevamiss' ),
								'choices' => range( 0, 23 ),
							),
							array(
								'name'    => 'weekly_times[minutes][]',
								'value'   => array(),
								'type'    => 'select',
								'class'   => 'daily-times-minute',
								'id'   => 'daily-times-minute',
								'label'   => __( 'Minute', 'nevamiss' ),
								'choices' => range( 1, 60, 1 ),
							),
						),
					),
					'monthly' => array(
						'name'              => 'monthly_times[days][]',
						'value'             => array(),
						'type'              => 'select-group',
						'class'             => 'monthly-times',
						'id'             => 'monthly-times',
						'choices'           => range( 1, $this->month_days( date( 'm' ) ) ),
						'label'             => __( 'Monthly Times', 'nevamiss' ),
						'has_multiple'      => true,
						'complement_fields' => array(
							array(
								'name'    => 'monthly_times[hours][]',
								'value'   => array(),
								'type'    => 'select',
								'class'   => 'monthly-daily-times',
								'id'   => 'monthly-daily-times',
								'label'   => __( 'at', 'nevamiss' ),
								'choices' => range( 0, 23 ),
							),
							array(
								'name'    => 'monthly_times[minutes][]',
								'value'   => array(),
								'type'    => 'select',
								'class'   => 'monthly-times-minute',
								'id'      => 'monthly-times-minute',
								'label'   => __( 'Minute', 'nevamiss' ),
								'choices' => range( 1, 60, 5 ),
							),
						),
					),
				),
			),
			array(
				'name'  => 'social_media_tags',
				'value' => '',
				'class' => 'social-media-tags',
				'id'    => 'social-media-tags',
				'type'  => 'textarea',
				'label' => __( 'Network Tags', 'nevamiss' ),
			),
			array(
				'name'     => 'network_accounts[]',
				'value'    => array(0),
				'class'    => 'network-accounts',
				'id'       => 'network-accounts',
				'type'     => 'select',
				'label'    => __( 'Network Accounts', 'nevamiss' ),
				'choices'  => $this->accounts(),
				'multiple' => true,
			),
			array(
				'name'  => 'query_args[post_per_page]',
				'value' => 1,
				'class' => 'share-count',
				'type'  => 'number',
				'min'   => 1,
				'max'   => 5,
				'label' => __( 'Number of post to share at a time', 'nevamiss' ),
			),
			array(
				'name'     => 'query_args[post_type][]',
				'value'    => array( 'post' ),
				'class'    => 'post-type',
				'id'       => 'post-type',
				'type'     => 'select',
				'choices'  => $this->post_types(),
				'multiple' => true,
				'label'    => __( 'Post Types', 'nevamiss' ),
			),
			array(
				'name'     => 'query_args[taxonomies][]',
				'value'    => array( 'category' ),
				'class'    => 'taxonomies',
				'id'       => 'taxonomies',
				'type'     => 'select',
				'choices'  => $this->taxonomies(),
				'multiple' => true,
				'label'    => __( 'Taxonomies', 'nevamiss' ),
			),
			array(
				'name'  => 'query_args[post_ids]',
				'value' => array(),
				'class' => 'post-ids',
				'type'  => 'text',
				'label' => __( 'Post IDs', 'nevamiss' ),
			),
		);
	}

	private function accounts(): array {
		$accounts = $this->account_repository->get_all();

		if ( ! $accounts ) {
			return array(
				'SwimGhana',
				'Nevafade',
				'Nice Bracelets',
			);
		}

		return array_reduce(
			$accounts,
			static function ( array $acc, array $account ) {
				$acc[ $account['id'] ] = $account['name'];
				return $acc;
			},
			array()
		);
	}

	private function post_types(): array {
		return array_reduce(
			get_post_types( array(), 'objects' ),
			static function ( array $acc, \WP_Post_Type $post_type ) {
				$acc[ $post_type->name ] = $post_type->label;
				return $acc;
			},
			array()
		);
	}

	private function taxonomies(): array {
		return get_taxonomies();
	}

	private function month_days( string $month ): int {
		if ( 2 === (int) $month ) {
			return Utils::is_leap_year( (int) date( 'Y' ) ) ? 29 : 28;
		}
		$total_days_months = array(
			31 => array( 1, 3, 5, 7, 8, 10, 12 ),
			30 => array( 4, 6, 9, 11 ),
		);
		foreach ( $total_days_months as $day => $months ) {
			if ( in_array( (int) $month, $months ) ) {
				return $day;
			}
		}
		return 30;
	}

    /**
     *
     * @throws \Exception
     */
    public function maybe_save_form(): void {
		if ( ! isset( $_POST['schedule_name'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'nevamiss_create_schedule' ) ) {
			return;
		}

		$data = $this->schedule_repository->allowed_data( $_POST );

		$validated_data = $this->validate($data);

		if(!empty($this->validator->errors())){
			return;
		}

		$data = $this->format_dates( $validated_data );

		$data = $this->array_to_json( $data );

		$this->schedule_repository->create( $data );
	}

	private function format_dates( array $data ): array
	{
		$day_times = array(
			'daily_times'   => $data['daily_times'] ?? null,
			'weekly_times'  => $data['weekly_times'] ?? null,
			'monthly_times' => $data['monthly_times'] ?? null,
		);
		foreach ( $day_times as $key => $day_time ) {
			if ( ! $day_time ) {
				continue;
			}

			['minutes' => $minutes, 'hours' => $hours ] = $day_time;

			if ( $key === 'daily_times' ) {
				$formatted_times = $this->format_daily_times( $hours, $minutes );
				$data[ $key ] = $this->ensure_unique_date($formatted_times);

				continue;
			}

			$formatted_times = $this->format_weekly_monthly_times( $day_time['days'], $hours, $minutes );

			$data[ $key ] = $this->ensure_unique_date($formatted_times);

		}
		if(isset($data['one_time_schedule'])){
			$data['one_time_schedule'] = array_unique($data['one_time_schedule']);
		}

		return $data;
	}

	private function format_daily_times( mixed $hours, mixed $minutes ): array {
		$daily_times = array();

		foreach ( $hours as $index => $hour ) {
			$daily_times[] = array(
				'hour'   => $hour,
				'minute' => $minutes[ $index ],
			);
		}
		return $daily_times;
	}

	private function format_weekly_monthly_times( array $days, array $hours, array $minutes ): array {
		$day_times = array();
		foreach ( $days as $index => $day ) {
			$daily_times                           = $this->format_daily_times( $hours, $minutes );
			['hour' => $hour, 'minute' => $minute] = $daily_times[ $index ];
			$day_times[]                           = array(
				'day'    => $day,
				'hour'   => $hour,
				'minute' => $minute,
			);
		}
		return $day_times;
	}

	private function array_to_json( array $data ): array {
		foreach ( $data as $key => $datum ) {
			if ( is_array( $datum ) ) {
				$data[ $key ] = json_encode( $datum );
			}
		}
		return $data;
	}

	private function validate(array $data): array
	{
		$validated_data = [];
		foreach ($this->schedule_repository->allow_columns() as $key){
			$datum = $data[$key] ?? null;
			if(!$datum){
				continue;
			}
			$validated_data[$key] = $this->validation_func($key)($datum);
		}
		// Make sure either of the fields is not null
		$one_has_value = false;
		foreach (['daily_times', 'weekly_times', 'monthly_times', 'one_time_schedule'] as $required_field){
			if( isset($validated_data[$required_field]) ){
				$one_has_value = true;
			}

		}

		if( !$one_has_value ){
			$this->validator->add_error('Either one of the repeat frequency sub field must have a value');
		}
		return $validated_data;
	}

	private function validation_func(string $field, ): \Closure
	{
		return [
			'schedule_name' => function(?string $schedule_name) {
				$this->validator->validate_string('schedule_name', $schedule_name, 4);
				return $this->validator->sanitize_string($schedule_name);
			},
			'repeat_frequency' => function(?string $repeat_frequency) {
				$this->validator->validate_string('repeat_frequency', $repeat_frequency, 4);
				return $this->validator->sanitize_string($repeat_frequency);
			},
			'start_date' => function(?string $start_date) {
				if(!$start_date){
					return null;
				}
				$this->validator->validate_date('start_date', $start_date);
				return $this->validator->sanitize_date($start_date);

			},
			'daily_times' => function(?array $daily_times) {
				if(!$daily_times || !$daily_times['hours']){
					return null;
				}
				$this->validator->validate_assoc_array_of_numbers('daily_times', $daily_times);

				return $this->validator->sanitize_assoc_array_of_numbers($daily_times);

			},
			'weekly_times' => function(?array $weekly_times) {
				if(!$weekly_times){
					return null;
				}

				$weekly_times['days'] = $this->validator->sanitize_array_of_string($weekly_times['days']);

				$weekly_times['hours'] = array_map(function($hour){
					return $this->validator->sanitize_number($hour);
				}, $weekly_times['hours']);

				$weekly_times['minutes'] = array_map(function($minute){
					return $this->validator->sanitize_number($minute);
				}, $weekly_times['minutes']);

				return $weekly_times;
			},
			'monthly_times' => function(?array $monthly_times) {
				if(!$monthly_times){
					return null;
				}
				$this->validator->validate_assoc_array_of_numbers('monthly_times', $monthly_times);

				return $this->validator->sanitize_assoc_array_of_numbers($monthly_times);
			},
			'query_args' => function(?array $query_args) {
				if(empty($query_args)){
					$this->validator->add_error('Query args are required');
					return null;
				}

				return $this->validator->sanitize_array_of_string($query_args);
			},
			'one_time_schedule' => function(?array $one_time_schedule) {

				if(!$one_time_schedule || !$one_time_schedule[0]){
					return null;
				}

				return array_map(function($date){
					return $this->validator->sanitize_date($date, 'Y-m-d H:s');
				}, $one_time_schedule);

			},
			'network_accounts' => function(?array $network_accounts) {
				if(empty($network_accounts)){
					$this->validator->add_error('You need to select at least one network account');
					return null;
				}

				return $this->validator->sanitize_array_of_string($network_accounts);
			},
			'social_media_tags' => function(?string $social_media_tags) {
				if(!$social_media_tags){
					return null;
				}
				$this->validator->validate_string('social_media_tags', $social_media_tags);
				return $this->validator->sanitize_string($social_media_tags);
			},

		][$field];
	}

	private function ensure_unique_date(array $dates): array
	{
		$counted = [];

		$unique_date = [];
		foreach ($dates as $date){
			$time = "{$date['hour']}-{$date['minute']}";

			if(isset($date['day'])){
				$time = "{$date['day']}-$time";
			}

			if(isset($counted[$time])){
				continue;
			}
			$unique_date[] = $date;

			$counted[$time] = 1;
		}
		return $unique_date;
	}
}
