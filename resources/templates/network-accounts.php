<?php

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Nevamiss\presentation\Tabs\Network_Accounts_Tab;

/**
 * @var Network_Accounts_Tab $this
 *
 */

?>
<div class="network-accounts">
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

	<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" >

		<?php $this->table_list()->search_box( __( 'Search Accounts', 'nevamiss' ), 'network-accounts' ); ?>
		<input type="hidden" name="page" value="nevamiss-settings">
		<input type="hidden" name="tab" value="<?php echo esc_attr($this->slug()) ?>">
        <input type="hidden" name="model_name" value="network-accounts">

		<?php $this->table_list()->display(); ?>

	</form>


	<div class="clear"></div>
</div>

