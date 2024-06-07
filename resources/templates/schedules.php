<?php

declare(strict_types=1);

use Nevamiss\Presentation\Pages\Schedules_Page;

/**
 * @var Schedules_Page $this
 */

var_dump("here");
?>

<hr class="wp-header-end">

<?php $this->table_list->views(); ?>

<form method="get">

<?php $this->table_list->search_box( __( 'Search Schedules' ), 'user' ); ?>

<?php $this->table_list->display(); ?>

</form>

<div class="clear"></div>
