<?php

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Nevamiss\Presentation\Pages\Schedules_Page;

/**
 * @var Schedules_Page $this
 */

?>
<div class="wrap">
    <?php $this->notices(); ?>

    <p>
        <h1 class="wp-heading-inline">
            <?php echo esc_html($this->title()); ?>
        </h1>
        <?php $this->new_link(); ?>
    </p>

    <hr class="wp-header-end">
    <?php $this->table_list->prepare_items(); ?>

    <?php $this->table_list->views(); ?>

    <form action="<?php echo esc_url(admin_url('admin-post.php?action=nevamiss_schedules_delete_action')); ?>">
        <?php $this->table_list->search_box( __( 'Search Schedules', 'nevamiss' ), 'schedules' ); ?>

        <input type="hidden" name="action" value="nevamiss_schedules_delete_action">

		<?php $this->table_list->display(); ?>

    </form>


    <div class="clear"></div>
</div>

