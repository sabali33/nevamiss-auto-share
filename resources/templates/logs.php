<?php

declare(strict_types=1);

use Nevamiss\Presentation\Tabs\Logs_Tab;

/**
 * @var Logs_Tab $this
 */

?>

<div class="logs">
	<p>
		<h1 class="wp-heading-inline">
			<?php echo esc_html($this->label()); ?>
		</h1>
	</p>
	<hr class="wp-header-end">
	<?php $this->table_list()->prepare_items(); ?>

	<?php $this->table_list()->views();?>
	<form action="">

		<?php $this->table_list()->search_box( __( 'Search Logs' ), 'nevamiss-logs' ); ?>
		<input type="hidden" name="page" value="nevamiss-settings">
		<input type="hidden" name="tab" value="<?php echo esc_attr($this->slug()) ?>">

		<?php $this->table_list()->display(); ?>

	</form>
</div>
