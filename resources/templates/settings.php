<?php

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Nevamiss\Presentation\Pages\Settings_Page;
use function Nevamiss\sanitize_text_input_field;

/**
 * @var Settings_Page $this
 */

?>

<?php

$active_tab = sanitize_text_input_field('tab') ?? 'general';
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

                echo wp_kses(
                        $tab->link($active_tab)->render(),
                        array(
                                'a' => array(
                                    'href' => true,
                                    'title' => true,
                                    'class' => true
                                )
                        )
                );
            }
        ?>
    </h2>

    <div class="tab-content">
        <?php
            echo wp_kses_post($this->render_tab($active_tab)?->render());
        ?>
    </div>
</div>
