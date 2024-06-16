<?php

declare(strict_types=1);

use Nevamiss\Presentation\Pages\Schedules_Page;

/**
 * @var Schedules_Page $this
 */


?>
<div class="wrap">
    <p>
        <h1 class="wp-heading-inline">
            <?php echo esc_html($this->title); ?>
        </h1>
        <?php $this->new_link(); ?>
    </p>


    <hr class="wp-header-end">
    <?php $this->table_list->prepare_items(); ?>

    <?php $this->table_list->views(); ?>

    <form method="get">

        <?php $this->table_list->search_box( __( 'Search Schedules' ), 'user' ); ?>

        <?php $this->table_list->display(); ?>

    </form>

    <div class="clear"></div>
</div>

