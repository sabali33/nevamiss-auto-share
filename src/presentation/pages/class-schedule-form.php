<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Domain\Entities\Schedule;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Components\Component_Runner;
use Nevamiss\Presentation\Components\Input_Fields\Input;
use Nevamiss\Presentation\Components\Input_Fields\Select_Field;
use Nevamiss\Presentation\Components\Input_Fields\Select_Group_Field;
use Nevamiss\Presentation\Components\Input_Fields\TextArea;
use Nevamiss\Presentation\Components\Renderable;
use Nevamiss\Presentation\Components\Wrapper;
use Nevamiss\Presentation\Utils;
use Nevamiss\Services\Form_Validator;

class Schedule_Form extends Page {

	public const TEMPLE_PATH = 'templates/schedule-form';
	public const SLUG        = 'edit-schedule';
	private ?Schedule $schedule;

	/**
	 * @throws Not_Found_Exception
	 */
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

		$this->schedule = ( isset( $_REQUEST['schedule_id'] ) && $_REQUEST['schedule_id'] ) ?
		$this->schedule_repository->get( (int) $_REQUEST['schedule_id'] ) : null;
	}

	public function factory(): Factory {
		return $this->factory;
	}

	public function schedule() {
		return $this->schedule;
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function render_field( array $field ): Component {

		$field_class = match ( $field['type'] ) {
			'select' => Select_Field::class,
			'textarea' => TextArea::class,
			'select-group' => Select_Group_Field::class,
			default => Input::class,
		};

		$sub_fields_components = array();
		if ( isset( $field['sub_fields'] ) ) {

			foreach ( $field['sub_fields'] as $key => $sub_fields ) {

				foreach ( $sub_fields as $sub_field ) {
					if ( empty( $sub_field ) ) {
						continue;
					}
					$selected_class = $field['value'] === $key ? ' active' : '';

					if ( str_contains( $key, '!' ) ) {

						if ( "!{$field['value']}" !== $key ) {
							$selected_class = ' active';
						}
					}
					$has_multiple   = isset( $sub_field['has_multiple'] ) && $sub_field['has_multiple'];
					$can_be_removed = isset( $sub_field['can_be_removed'] ) && $sub_field['can_be_removed'];
					$parent_value   = esc_attr( $key );

					$sub_field_elements = array();
					if ( $can_be_removed ) {
						$sub_field_elements[] = $this->factory()->component(
							Wrapper::class,
							array(
								'tag'        => 'button',
								'attributes' => array(
									'class' => 'remove',
								),
								'text'       => __( 'X', 'nevamiss' ),
							)
						);
					}
					$sub_field_elements[] = $this->render_field( $sub_field );

					if ( $has_multiple ) {
						$sub_field_elements[] = $this->factory()->component(
							Wrapper::class,
							array(
								'tag'        => 'button',
								'attributes' => array(
									'class' => 'add-field-group button',
								),
								'text'       => __( 'Add', 'nevamiss' ),
							)
						);
					}

					$sub_fields_components[] = $this->factory()->component(
						Wrapper::class,
						array(
							'attributes' => array(
								'class'                 => "sub-field-wrapper{$selected_class} $key",
								'data-repeat-frequency' => $parent_value,
							),

						),
						$sub_field_elements
					);
				}
			}
		}

		if ( empty( $sub_fields_components ) ) {
			return $this->factory()->component( $field_class, $field );
		}
		return $this->factory()->component(
			Component_Runner::class,
			array(),
			array(
				$this->factory()->component( $field_class, $field ),
				...$sub_fields_components,
			)
		);
	}

	public function fields(): array {

		return array(
			array(

				'label' => __( 'Name', 'nevamiss' ),
				'name'  => 'schedule_name',
				'value' => $this->schedule ? $this->schedule->name() : '',
				'class' => 'schedule-name',
				'type'  => 'text',

			),
			array(
				'name'       => 'repeat_frequency',
				'value'      => $this->schedule ? $this->schedule->repeat_frequency() : 'none',
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
					'!none'   => array(
						array(
							'name'         => 'start_date',
							'value'        => $this->schedule ? $this->schedule->start_date() : '',
							'class'        => 'start-date date',
							'type'         => 'date',
							'label'        => __( 'Start Date', 'nevamiss' ),
							'has_multiple' => false,
						),
					),
					'none'    => $this->one_time_fields(),
					'daily'   => $this->daily_fields(),
					'weekly'  => $this->weekly_fields(),
					'monthly' => $this->monthly_fields(),
				),
			),
			array(
				'name'  => 'social_media_tags',
				'value' => $this->schedule ? $this->schedule->social_media_tags() : '',
				'class' => 'social-media-tags',
				'id'    => 'social-media-tags',
				'type'  => 'textarea',
				'label' => __( 'Network Tags', 'nevamiss' ),
			),
			array(
				'name'     => 'network_accounts[]',
				'value'    => $this->schedule ? $this->schedule->network_accounts() : array( 0 ),
				'class'    => 'network-accounts',
				'id'       => 'network-accounts',
				'type'     => 'select',
				'label'    => __( 'Network Accounts', 'nevamiss' ),
				'choices'  => $this->accounts(),
				'multiple' => true,
			),
			array(
				'name'  => 'query_args[posts_per_page]',
				'value' => $this->schedule ? $this->schedule->query_args()['posts_per_page'] : 1,
				'class' => 'share-count',
				'type'  => 'number',
				'min'   => 1,
				'max'   => 5,
				'label' => __( 'Number of post to share at a time', 'nevamiss' ),
			),
			array(
				'name'     => 'query_args[post_type][]',
				'value'    => $this->schedule ? $this->schedule->query_args()['post_type'] : array( 'post' ),
				'class'    => 'post-type',
				'id'       => 'post-type',
				'type'     => 'select',
				'choices'  => $this->post_types(),
				'multiple' => true,
				'label'    => __( 'Post Types', 'nevamiss' ),
			),
			array(
				'name'     => 'query_args[taxonomies][]',
				'value'    => $this->schedule ? $this->schedule->query_args()['taxonomies'] : array( 'category' ),
				'class'    => 'taxonomies',
				'id'       => 'taxonomies',
				'type'     => 'select',
				'choices'  => $this->taxonomies(),
				'multiple' => true,
				'label'    => __( 'Taxonomies', 'nevamiss' ),
			),
			array(
				'name'  => 'query_args[post_ids]',
				'value' => $this->schedule ? $this->schedule->query_args()['post_ids'] : array(),
				'class' => 'post-ids',
				'type'  => 'text',
				'label' => __( 'Post IDs', 'nevamiss' ),
			),
			array(
				'name'    => 'query_args[orderby]',
				'value'   => $this->schedule ? $this->schedule->query_args()['orderby'] : array( 'date' ),
				'class'   => 'sort-by',
				'type'    => 'select',
				'label'   => __( 'Sort posts by', 'nevamiss' ),
				'choices' => $this->sort_posts(),
			),
		);
	}

	private function accounts(): array {
		$accounts = $this->account_repository->get_all();

		return array_reduce(
			$accounts,
			static function ( array $acc, Network_Account $account ) {
				$acc[ $account->id() ] = $account->name();
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

		$validated_data = $this->validate( $data );

		if ( ! empty( $this->validator->errors() ) ) {

			wp_admin_notice(
				join( ', ', $this->validator->errors() ),
				array(
					'type'               => 'error',
					'dismissible'        => false,
					'additional_classes' => array( 'inline', 'notice-alt' ),
				)
			);
			return;
		}

		$data = $this->format_dates( $validated_data );

		$data = $this->array_to_json( $data );

		$schedules_url = admin_url( 'admin.php?page=schedules' );

		try {
			$this->schedule_repository->create( $data );

			$message = sprintf( __( "Successfully created a schedule <a href='%s'>back</a>", 'nevamiss' ), esc_url( $schedules_url ) );
			$type    = 'success';

		} catch ( \Exception $exception ) {
			$message = $exception->getMessage();
			$type    = 'error';
		}
		wp_admin_notice(
			$message,
			array(
				'type'               => $type,
				'dismissible'        => false,
				'additional_classes' => array( 'inline', 'notice-alt' ),
			)
		);
	}

	/**
	 * @throws \Exception
	 */
	public function update_form(): void {
		if ( ! isset( $_POST['schedule_name'] ) || ! $this->schedule() ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'nevamiss_create_schedule' ) ) {
			return;
		}

		$data = $this->schedule_repository->allowed_data( $_POST );

		$validated_data = $this->validate( $data );

		if ( ! empty( $this->validator->errors() ) ) {

			wp_admin_notice(
				join( ', ', $this->validator->errors() ),
				array(
					'type'               => 'error',
					'dismissible'        => false,
					'additional_classes' => array( 'inline', 'notice-alt' ),
				)
			);
			return;
		}

		$data = $this->format_dates( $validated_data );

		$data = $this->array_to_json( $data );

		$schedules_url = admin_url( 'admin.php?page=schedules' );

		try {
			$this->schedule_repository->update( $this->schedule()->id(), $data );
			$message = sprintf( __( "Successfully updated a schedule <a href='%s'>back</a>", 'nevamiss' ), esc_url( $schedules_url ) );
			$type    = 'success';

			do_action( 'nevamiss_after_schedule_updated', $this->schedule );

		} catch ( \Exception $exception ) {
			$message = $exception->getMessage();
			$type    = 'error';
		}
		wp_admin_notice(
			$message,
			array(
				'type'               => $type,
				'dismissible'        => false,
				'additional_classes' => array( 'inline', 'notice-alt' ),
			)
		);
		$this->schedule = $this->schedule_repository->get( $this->schedule->id() );
	}

	private function format_dates( array $data ): array {
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
				$data[ $key ]    = $this->ensure_unique_date( $formatted_times );

				continue;
			}

			$formatted_times = $this->format_weekly_monthly_times( $day_time['days'], $hours, $minutes );

			$data[ $key ] = $this->ensure_unique_date( $formatted_times );

		}
		if ( isset( $data['one_time_schedule'] ) ) {
			$data['one_time_schedule'] = array_unique( $data['one_time_schedule'] );
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

	private function validate( array $data ): array {
		$validated_data = array();

		foreach ( $this->schedule_repository->allow_columns() as $key ) {
			$datum = $data[ $key ] ?? null;

			if ( $datum === null && ! $this->schedule ) {
				continue;
			}
			$validated_data[ $key ] = $this->validation_func( $key )( $datum );
		}

		// Make sure either of the fields is not null
		$one_has_value = false;
		foreach ( array( 'daily_times', 'weekly_times', 'monthly_times', 'one_time_schedule' ) as $required_field ) {
			if ( isset( $validated_data[ $required_field ] ) ) {
				$one_has_value = true;
			}
		}

		if ( ! $one_has_value ) {
			$this->validator->add_error( 'Either one of the repeat frequency sub field must have a value' );
		}
		return $validated_data;
	}

	private function validation_func( string $field, ): \Closure {
		return array(
			'schedule_name'     => function ( ?string $schedule_name ) {

				$this->validator->validate_string( 'schedule_name', $schedule_name, 4 );
				return $this->validator->sanitize_string( $schedule_name );
			},
			'repeat_frequency'  => function ( ?string $repeat_frequency ) {
				$this->validator->validate_string( 'repeat_frequency', $repeat_frequency, 4 );
				return $this->validator->sanitize_string( $repeat_frequency );
			},
			'start_date'        => function ( ?string $start_date ) {
				if ( ! $start_date ) {
					return null;
				}
				$this->validator->validate_date( 'start_date', $start_date );
				return $this->validator->sanitize_date( $start_date );
			},
			'daily_times'       => function ( ?array $daily_times ) {
				if ( ! $daily_times || ! $daily_times['hours'] ) {
					return null;
				}
				$this->validator->validate_assoc_array_of_numbers( 'daily_times', $daily_times );

				return $this->validator->sanitize_assoc_array_of_numbers( $daily_times );
			},
			'weekly_times'      => function ( ?array $weekly_times ) {
				if ( ! $weekly_times ) {
					return null;
				}

				$weekly_times['days'] = $this->validator->sanitize_array_of_string( $weekly_times['days'] );

				$weekly_times['hours'] = array_map(
					function ( $hour ) {
						return $this->validator->sanitize_number( $hour );
					},
					$weekly_times['hours']
				);

				$weekly_times['minutes'] = array_map(
					function ( $minute ) {
						return $this->validator->sanitize_number( $minute );
					},
					$weekly_times['minutes']
				);

				return $weekly_times;
			},
			'monthly_times'     => function ( ?array $monthly_times ) {
				if ( ! $monthly_times ) {
					return null;
				}
				$this->validator->validate_assoc_array_of_numbers( 'monthly_times', $monthly_times );

				return $this->validator->sanitize_assoc_array_of_numbers( $monthly_times );
			},
			'query_args'        => function ( ?array $query_args ) {
				if ( empty( $query_args ) ) {
					$this->validator->add_error( 'Query args are required' );
					return null;
				}

				return $this->validator->sanitize_array_of_string( $query_args );
			},
			'one_time_schedule' => function ( ?array $one_time_schedule ) {

				if ( ! $one_time_schedule || ! $one_time_schedule[0] ) {
					return null;
				}

				return array_map(
					function ( $date ) {
						return $this->validator->sanitize_date( $date, 'Y-m-d H:s' );
					},
					$one_time_schedule
				);
			},
			'network_accounts'  => function ( ?array $network_accounts ) {
				if ( empty( $network_accounts ) ) {
					$this->validator->add_error( 'You need to select at least one network account' );
					return null;
				}

				return $this->validator->sanitize_array_of_string( $network_accounts );
			},
			'social_media_tags' => function ( ?string $social_media_tags ) {
				if ( ! $social_media_tags ) {
					return null;
				}
				$this->validator->validate_string( 'social_media_tags', $social_media_tags );
				return $this->validator->sanitize_string( $social_media_tags );
			},

		)[ $field ];
	}

	private function ensure_unique_date( array $dates ): array {
		$counted = array();

		$unique_date = array();
		foreach ( $dates as $date ) {
			$time = "{$date['hour']}-{$date['minute']}";

			if ( isset( $date['day'] ) ) {
				$time = "{$date['day']}-$time";
			}

			if ( isset( $counted[ $time ] ) ) {
				continue;
			}
			$unique_date[] = $date;

			$counted[ $time ] = 1;
		}
		return $unique_date;
	}

	private function one_time_fields(): array {
		if ( ! $this->schedule || ! $this->schedule->one_time_schedule() ) {
			return array(
				array(
					'name'         => 'one_time_schedule[]',
					'type'         => 'date',
					'class'        => 'datetime date-time',
					'label'        => __( 'Select Date' ),
					'value'        => '',
					'has_multiple' => true,
				),
			);
		}

		$fields = array();
		foreach ( $this->schedule->one_time_schedule() as $value ) {
			$fields[] = array(
				'name'  => 'one_time_schedule[]',
				'type'  => 'date',
				'class' => 'datetime date-time',
				'label' => __( 'Select Date' ),
				'value' => $value,
			);
		}
		return $fields;
	}

	private function daily_fields(): array {
		if ( ! $this->schedule || ! $this->schedule->daily_times() ) {
			return array(
				array(
					'name'              => 'daily_times[hours][]',
					'value'             => array(),
					'type'              => 'select-group',
					'class'             => 'daily-times',
					'label'             => __( 'Hour', 'nevamiss' ),
					'choices'           => $this->hours(),
					'has_multiple'      => true,
					'complement_fields' => array(
						array(
							'name'    => 'daily_times[minutes][]',
							'value'   => array(),
							'type'    => 'select',
							'class'   => 'daily-times-minute',
							'label'   => __( 'Minute', 'nevamiss' ),
							'choices' => $this->minutes(),
							'id'      => 'daily-minute',
						),
					),
				),
			);
		}

		$fields = array();

		foreach ( $this->schedule->daily_times() as $index => $time ) {
			$last_field = $index === ( count( $this->schedule->daily_times() ) - 1 );
			$fields[]   = array(
				'name'              => 'daily_times[hours][]',
				'value'             => $time['hour'],
				'type'              => 'select-group',
				'class'             => 'daily-times',
				'label'             => __( 'Hour', 'nevamiss' ),
				'choices'           => $this->hours(),
				'has_multiple'      => $last_field,
				'can_be_removed'    => ! $last_field,
				'complement_fields' => array(
					array(
						'name'    => 'daily_times[minutes][]',
						'value'   => $time['minute'],
						'type'    => 'select',
						'class'   => 'daily-times-minute',
						'label'   => __( 'Minute', 'nevamiss' ),
						'choices' => $this->minutes(),
						'id'      => 'daily-minute',
					),
				),
			);
		}
		return $fields;
	}

	private function weekly_fields(): array {
		if ( ! $this->schedule || ! $this->schedule->weekly_times() ) {
			return array(
				array(
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
							'id'      => 'weekly-daily-hour',
							'label'   => __( 'at', 'nevamiss' ),
							'choices' => $this->hours(),
						),
						array(
							'name'    => 'weekly_times[minutes][]',
							'value'   => array(),
							'type'    => 'select',
							'class'   => 'daily-times-minute',
							'id'      => 'daily-times-minute',
							'label'   => __( 'Minute', 'nevamiss' ),
							'choices' => $this->minutes(),
						),
					),
				),
			);
		}
		$fields = array();

		foreach ( $this->schedule->weekly_times() as $index => $week_time ) {
			$last_field = $index === ( count( $this->schedule->weekly_times() ) - 1 );
			$fields[]   = array(
				'name'              => 'weekly_times[days][]',
				'value'             => $week_time['day'],
				'type'              => 'select-group',
				'class'             => 'weekly-times',
				'id'                => "weekly-times-$index",
				'label'             => __( 'Weekly Times', 'nevamiss' ),
				'has_multiple'      => $last_field,
				'can_be_removed'    => ! $last_field,
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
						'value'   => $week_time['hour'],
						'type'    => 'select',
						'class'   => 'weekly-daily-hour',
						'id'      => "weekly-daily-hour-$index",
						'label'   => __( 'at', 'nevamiss' ),
						'choices' => $this->hours(),
					),
					array(
						'name'    => 'weekly_times[minutes][]',
						'value'   => $week_time['minute'],
						'type'    => 'select',
						'class'   => 'daily-times-minute',
						'id'      => "daily-times-minute-$index",
						'label'   => __( 'Minute', 'nevamiss' ),
						'choices' => $this->minutes(),
					),
				),
			);
		}

		return $fields;
	}

	private function monthly_fields(): array {
		if ( ! $this->schedule || ! $this->schedule->monthly_times() ) {
			return array(
				array(
					'name'              => 'monthly_times[days][]',
					'value'             => array(),
					'type'              => 'select-group',
					'class'             => 'monthly-times',
					'id'                => 'monthly-times',
					'choices'           => range( 1, $this->month_days( date( 'm' ) ) ),
					'label'             => __( 'On day', 'nevamiss' ),
					'has_multiple'      => true,
					'complement_fields' => array(
						array(
							'name'    => 'monthly_times[hours][]',
							'value'   => array(),
							'type'    => 'select',
							'class'   => 'monthly-daily-times',
							'id'      => 'monthly-daily-times',
							'label'   => __( 'at', 'nevamiss' ),
							'choices' => $this->hours(),
						),
						array(
							'name'    => 'monthly_times[minutes][]',
							'value'   => array(),
							'type'    => 'select',
							'class'   => 'monthly-times-minute',
							'id'      => 'monthly-times-minute',
							'label'   => __( 'Minute', 'nevamiss' ),
							'choices' => $this->minutes(),
						),
					),
				),
			);
		}
		$fields = array();

		foreach ( $this->schedule->monthly_times() as $index => $monthly_time ) {
			$last_field = $index === ( count( $this->schedule->monthly_times() ) - 1 );
			$fields[]   = array(
				'name'              => 'monthly_times[days][]',
				'value'             => $monthly_time['day'],
				'type'              => 'select-group',
				'class'             => 'monthly-times',
				'id'                => "monthly-times-$index",
				'choices'           => range( 1, $this->month_days( date( 'm' ) ) ),
				'label'             => __( 'On day', 'nevamiss' ),
				'has_multiple'      => $last_field,
				'can_be_removed'    => ! $last_field,
				'complement_fields' => array(
					array(
						'name'    => 'monthly_times[hours][]',
						'value'   => $monthly_time['hour'],
						'type'    => 'select',
						'class'   => 'monthly-daily-times',
						'id'      => "monthly-daily-times-$index",
						'label'   => __( 'at', 'nevamiss' ),
						'choices' => $this->hours(),
					),
					array(
						'name'    => 'monthly_times[minutes][]',
						'value'   => $monthly_time['minute'],
						'type'    => 'select',
						'class'   => 'monthly-times-minute',
						'id'      => "monthly-times-minute-$index",
						'label'   => __( 'Minute', 'nevamiss' ),
						'choices' => $this->minutes(),
					),
				),
			);
		}
		return $fields;
	}

	private function sort_posts(): array {
		$criteria = array(
			'newest'        => __( 'Newest', 'nevamiss' ),
			'post_title'    => __( 'Title', 'nevamiss' ),
			'oldest'        => __( 'Oldest', 'nevamiss' ),
			'modified_date' => __( 'Modified Date', 'nevamiss' ),
			'comment_count' => __( 'Comments Count', 'nevamiss' ),
			'rand'          => __( 'Random', 'nevamiss' ),
		);
		if ( $this->schedule() ) {
			$criteria['queue_order'] = __( 'Keep as ordered in queue', 'nevamiss' );
		}
		return $criteria;
	}

	/**
	 * @return array
	 */
	private function minutes(): array {
		return array_reduce(
			range( 1, 60, 1 ),
			function ( $acc, $curr ) {
				$acc[ $curr ] = $curr;
				return $acc;
			},
			array()
		);
	}

	/**
	 * @return array
	 */
	private function hours(): array {
		return range( 0, 23 );
	}
}
