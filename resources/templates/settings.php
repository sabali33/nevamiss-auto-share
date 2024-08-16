<?php

declare(strict_types=1);

use Nevamiss\Presentation\Pages\Settings_Page;

/**
 * @var Settings_Page $this
 */

?>

<?php

$active_tab = $_GET['tab'] ?? 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$tabs = $this->tabs();

?>
<div class="wrap">
    <?php $this->notices(); ?>
    <h2>
        <?php esc_html_e('Settings', 'nevamiss'); ?>
    </h2>
    <hr>
    <h2 class="nav-tab-wrapper">

        <?php
            foreach ($tabs as $tab){
                echo $tab->link($active_tab)->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        ?>
    </h2>

    <div class="tab-content">
        <?php
            echo $this->render_tab($active_tab)?->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
    </div>
</div>
