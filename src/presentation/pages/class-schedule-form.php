<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Presentation\Components\Input_Fields\Input;
use Nevamiss\Presentation\Components\Input_Fields\Select_Field;
use Nevamiss\Presentation\Components\Input_Fields\TextArea;

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

	public function render_field( array $field ): void {

		switch ( $field['type'] ) {
			case 'select':
				$field_class = Select_Field::class;
				break;
			case 'textarea':
				$field_class = TextArea::class;
				break;
			default:
				$field_class = Input::class;
				break;
		}

		echo $this->factory()->component( $field_class, $field )->render();

        if(isset($field['sub_fields'])){
            foreach ($field['sub_fields'] as $sub_field ){
                if( !empty($sub_field )){
                    $this->render_field($sub_field);
                }
            }
        }
	}

	public function fields(): array {
		return array(
			array(

				'label'     => __( 'Name', 'nevamiss' ),
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
						'name'                => 'daily_times',
						'value'               => '',
						'type'                => 'hidden',
						'class'               => 'daily-times',
						'label'               => __( 'Daily Times', 'nevamiss' ),
						'data-times-calendar' => true,
					),
					'weekly'  => array(
						'name'               => 'weekly_times',
						'value'              => '',
						'type'               => 'hidden',
						'class'              => 'weekly-times',
						'data-week-calendar' => true,
					),
					'monthly' => array(
						'name'               => 'monthly_times',
						'value'              => '',
						'type'               => 'hidden',
						'class'              => 'monthly-times',
						'data-week-calendar' => true,
					),
				),
			),
			array(
				'name'  => 'social_media_tags',
				'value' => '',
				'class' => 'social-media-tags',
				'type'  => 'textarea',
			),
			array(
				'name'     => 'network_accounts',
				'value'    => array(),
				'class'    => 'network-accounts',
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
				'name'     => 'query_args[post_type]',
				'value'    => array( 'post' ),
				'class'    => 'post-type',
				'type'     => 'select',
				'choices'  => $this->post_types(),
				'multiple' => true,
				'label'    => __( 'Post Types', 'nevamiss' ),
			),
			array(
				'name'     => 'query_args[taxonomies]',
				'value'    => array( 'category' ),
				'class'    => 'taxonomies',
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
			return array();
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

	private function taxonomies(): array
    {
		return get_taxonomies();
	}
}
