<?php

declare(strict_types=1);

use Nevamiss\presentation\Tabs\Network_Accounts_Tab;

/**
 * @var Network_Accounts_Tab $this
 *
 */

?>
<div class="network-accounts">

	<?php
        try {
            $deleted = $this->bulk_delete('network_accounts');

            if($deleted){
                $this->redirect([
                        'message' => __("Account deleted"),
                        'status' => 'success'
                ]);
            }

        }catch (Exception $exception){
            $this->redirect([
                'message' => $exception->getMessage(),
                'status' => 'error'
            ]);
        }

    ?>

	<p>
        <h1 class="wp-heading-inline">
            <?php echo esc_html($this->label()); ?>
        </h1>

	</p>
    <p>
        <?php echo $this->login_links(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </p>
	<hr class="wp-header-end">
	<?php $this->table_list()->prepare_items(); ?>

	<?php $this->table_list()->views();?>

	<form action="">

		<?php $this->table_list()->search_box( __( 'Search Accounts' ), 'network-accounts' ); ?>
		<input type="hidden" name="page" value="nevamiss-settings">
		<input type="hidden" name="tab" value="<?php echo esc_attr($this->slug()) ?>">

		<?php $this->table_list()->display(); ?>

	</form>


	<div class="clear"></div>
</div>

