<?php

declare(strict_types=1);

use Nevamiss\Presentation\Pages\Schedules_Page;

/**
 * @var Schedules_Page $this
 */


?>
<div class="wrap">
    <?php $this->notices(); ?>

    <?php $this->bulk_delete(); ?>

    <p>
        <h1 class="wp-heading-inline">
            <?php echo esc_html($this->title); ?>
        </h1>
        <?php $this->new_link(); ?>
    </p>

    <hr class="wp-header-end">
    <?php $this->table_list->prepare_items(); ?>

    <?php $this->table_list->views(); ?>

    <form action="">

        <?php $this->table_list->search_box( __( 'Search Schedules' ), 'schedules' ); ?>
        <input type="hidden" name="page" value="schedules">

	    <?php $this->table_list->display(); ?>

    </form>


    <div class="clear"></div>
</div>

