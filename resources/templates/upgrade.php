<?php

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Nevamiss\Presentation\Tabs\Upgrade_Tab;

/**
 * @var Upgrade_Tab $this
 */

?>

<div class="premium-features-wrap">
    <h2>
		<?php esc_html_e('** Premium Features **', 'nevamiss'); ?>
    </h2>
    <ul class="premium-feature-list">
		<?php foreach ($this->premium_feature_list() as $feature) : ?>
            <li>
				<?php echo esc_html($feature)." (Coming Soon)"; ?>
            </li>
		<?php endforeach; ?>

    </ul>

    <div class="convert-action">
        <a href="#" class="button button-primary"> <?php esc_html_e('Upgrade', 'nevamiss'); ?>  </a>
	    <?php esc_html_e(' or ', 'nevamiss'); ?>
        <a href="#" class="button button-default"> <?php esc_html_e('Donate', 'nevamiss'); ?> </a>
    </div>


</div>
