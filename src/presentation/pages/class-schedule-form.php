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

class Schedule_Form extends Page {

	public const TEMPLE_PATH = 'templates/schedule-form';
	public const SLUG        = 'edit-schedule';

	public function __construct(
		private Schedule_Repository $schedule_repository,
		private Network_Account_Repository $account_repository,
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

		switch ( $field['type'] ) {
			case 'select':
				$field_class = Select_Field::class;
				break;
			case 'textarea':
				$field_class = TextArea::class;
				break;
			case 'select-group':
				$field_class = Select_Group_Field::class;
				break;
			default:
				$field_class = Input::class;
				break;
		}

		echo $this->factory()->component( $field_class, $field )->render();

		if ( isset( $field['sub_fields'] ) ) {
			foreach ( $field['sub_fields'] as $sub_field ) {
				if ( ! empty( $sub_field ) ) {
					$this->render_field( $sub_field );
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
				'name'  => 'start_date',
				'value' => '',
				'class' => 'start-date',
				'type'  => 'date',
				'label' => __( 'Start Date', 'nevamiss' ),
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
					'none'    => array(),
					'daily'   => array(
						'name'              => 'daily_times[hours][]',
						'value'             => array(),
						'type'              => 'select-group',
						'class'             => 'daily-times',
						'label'             => __( 'Daily Times', 'nevamiss' ),
						'choices'           => range( 0, 23 ),
						'complement_fields' => array(
							array(
								'name'    => 'daily_times[minutes][]',
								'value'   => array(),
								'type'    => 'select',
								'class'   => 'daily-times-minute',
								'label'   => __( 'Minute', 'nevamiss' ),
								'choices' => range( 0, 60, 5 ),
								'id'      => 'daily-minute'
							),
						),
					),
					'weekly'  => array(
						'name'              => 'weekly_times[days][]',
						'value'             => '',
						'type'              => 'select-group',
						'class'             => 'weekly-times',
						'id'             => 'weekly-times',
						'label'             => __( 'Weekly Times', 'nevamiss' ),
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
								'choices' => range( 0, 60, 5 ),
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
								'choices' => range( 0, 60, 5 ),
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
				'value'    => array(),
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

		$data = $this->schedule_repository->validate_data( $_POST );

		$data = $this->format_dates( $data );

		$data = $this->array_to_json( $data );

		$this->schedule_repository->create( $data );
	}

	private function format_dates( array $data ): array
	{
		[
			'daily_times' => $daily_times,
			'weekly_times' => $weekly_times,
			'monthly_times' => $monthly_times,
		]          = $data;
		$day_times = array(
			'daily_times'   => $daily_times,
			'weekly_times'  => $weekly_times,
			'monthly_times' => $monthly_times,
		);
		foreach ( $day_times as $key => $day_time ) {
			if ( ! $day_time ) {
				continue;
			}

			['minutes' => $minutes, 'hours' => $hours ] = $day_time;

			if ( $key === 'daily_times' ) {
				$data[ $key ] = $this->format_daily_times( $hours, $minutes );
				continue;
			}

			$data[ $key ] = $this->format_weekly_monthly_times( $day_time['days'], $hours, $minutes );

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
}
