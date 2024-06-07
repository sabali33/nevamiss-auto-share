<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Repositories\Schedule_Repository;

class Schedules_Table_List extends \WP_List_Table{

    public function __construct(private Schedule_Repository $schedule_repository, $args = array())
    {
        parent::__construct([
            'singular' => 'schedule',
            'plural'   => 'schedules',
            'screen'   => $args['screen'] ?? null,
        ]);
    }
    public function prepare_items(): void
    {
        $search = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
        $schedules_per_page = 10;
        $paged = $this->get_pagenum();

        $args = [
            'per_page' => $schedules_per_page,
            'offset' => ( $paged -1 ) * $schedules_per_page,
            'search' => $search,
        ];

        if ( '' !== $args['search'] ) {
            $args['search'] = '*' . $args['search'] . '*';
        }

        if ( isset( $_REQUEST['orderby'] ) ) {
            $args['orderby'] = $_REQUEST['orderby'];
        }

        if ( isset( $_REQUEST['order'] ) ) {
            $args['order'] = $_REQUEST['order'];
        }

        $this->items = $this->schedule_repository->get_all($args);

        $this->set_pagination_args(
            array(
                'total_items' => $this->schedule_repository->get_total(),
                'per_page'    => $schedules_per_page,
            )
        );
    }
    public function no_items(): void
    {
        _e( 'No Schedules found.', 'nevamiss' );
    }

    /**
     * Gets a list of columns for the list table.
     *
     * @since 3.1.0
     *
     * @return string[] Array of column titles keyed by their column name.
     */
    public function get_columns(): array
    {
        return array(
            'cb'       => '<input type="checkbox" />',
            'name' => __( 'Name', 'nevamiss' ),
            'start_time'     => __( 'Start Time', 'nevamiss' ),
            'repeat_frequency'    => __( 'Repeat Frequency', 'nevamiss' ),
            'query'     => __( 'Query Args', 'nevamiss' ),
            'accounts'    => __( 'Accounts', 'nevamiss' ),
            'weekly_times'    => __( 'Weekly_times', 'nevamiss' ),
            'monthly_times'    => __( 'monthly_times', 'nevamiss' ),
            'daily_times'    => __( 'Daily times', 'nevamiss' ),
            'created_at'    => __( 'Created At', 'nevamiss' ),
        );
    }

    protected function get_sortable_columns(): array
    {
        return array(
            'name' => array( 'Name', false, __( 'Name', 'nevamiss' ), __( 'Table ordered by Name.' ), 'asc' ),
            'created_at'    => array( 'Created Date', false, __( 'Created Date', 'nevamiss' ), __( 'Table ordered by Created Date.', 'nevamiss' ) ),
        );
    }

    public function display_rows(): void
    {
        foreach ( $this->items as  $schedule ) {
            echo "\n\t" . $this->single_row( $schedule );
        }
    }
    protected function get_default_primary_column_name(): string
    {
        return 'name';
    }

}