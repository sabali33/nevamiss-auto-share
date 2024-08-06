<?php

declare(strict_types=1);

use Nevamiss\Presentation\Tabs\Stats_Tab;

/**
 * @var Stats_Tab $this
 */

?>
<div class="stats">
	<?php $this->bulk_delete('stats'); ?>

	<p>
	<h1 class="wp-heading-inline">
		<?php echo esc_html($this->label()); ?>
	</h1>

	</p>

	<hr class="wp-header-end">
	<?php $this->table_list()->prepare_items(); ?>

	<?php $this->table_list()->views();?>

	<form action="">

		<?php $this->table_list()->search_box( __( 'Search Accounts' ), 'stats' ); ?>
		<input type="hidden" name="page" value="nevamiss-settings">
		<input type="hidden" name="tab" value="<?php echo esc_attr($this->slug()) ?>">

		<?php $this->table_list()->display(); ?>

	</form>


	<div class="clear"></div>
</div>

