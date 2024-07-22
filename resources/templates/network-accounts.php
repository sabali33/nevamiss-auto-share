<?php

declare(strict_types=1);

use Nevamiss\presentation\Tabs\Network_Accounts_Tab;

/**
 * @var Network_Accounts_Tab $this
 *
 */

?>
<div class="network-accounts">
	<?php $this->notices(); ?>

	<?php
	try {
		$deleted = $this->bulk_delete();
        if($deleted){
	        wp_admin_notice(
		        __("Account deleted"),
		        array(
			        'type'               => 'success',
			        'dismissible'        => false,
			        'additional_classes' => array( 'inline', 'notice-alt' ),
		        )
	        );
        }

	}catch (Exception $exception){
		wp_admin_notice(
			$exception->getMessage(),
			array(
				'type'               => 'error',
				'dismissible'        => false,
				'additional_classes' => array( 'inline', 'notice-alt' ),
			)
		);
    }

    ?>

	<p>
	<h1 class="wp-heading-inline">
		<?php echo esc_html($this->label()); ?>
	</h1>

	</p>
    <p>
        <?php echo $this->login_links(); ?>
    </p>
	<hr class="wp-header-end">
	<?php $this->table_list->prepare_items(); ?>

	<?php $this->table_list->views();?>

	<form action="">

		<?php $this->table_list->search_box( __( 'Search Accounts' ), 'network-accounts' ); ?>
		<input type="hidden" name="page" value="nevamiss-settings">
		<input type="hidden" name="tab" value="<?php esc_attr_e($this->slug()) ?>">

		<?php $this->table_list->display(); ?>

	</form>


	<div class="clear"></div>
</div>

