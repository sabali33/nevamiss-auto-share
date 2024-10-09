<?php

declare(strict_types=1);

use Nevamiss\Presentation\Tabs\Stats_Tab;

/**
 * @var Stats_Tab $this
 */

?>
<div class="stats">

	<h1 class="wp-heading-inline">
		<?php echo esc_html($this->label()); ?>
	</h1>


	<hr class="wp-header-end">
	<?php $this->table_list()->prepare_items(); ?>

	<?php $this->table_list()->views();?>

	<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>">

		<?php $this->table_list()->search_box( __( 'Search Accounts', 'nevamiss' ), 'stats' ); ?>
		<input type="hidden" name="page" value="nevamiss-settings">
		<input type="hidden" name="tab" value="<?php echo esc_attr($this->slug()) ?>">
        <input type="hidden" name="model_name" value="stats">

		<?php $this->table_list()->display(); ?>

	</form>


	<div class="clear"></div>
</div>

