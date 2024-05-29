<?php
namespace Saas\Inc\Presentation;

use Saas\Saas;

class Presentation
{
    public function add_auto_share_page()
    {
        include SAAS_BASE . 'inc/admin/pages/options-page.php';
    }

    public function display_shared_posts()
    {
        include SAAS_BASE . 'inc/admin/pages/shared-posts.php';
    }

    public function add_networks_page()
    {
        include SAAS_BASE . 'inc/admin/pages/networks-page.php';
    }

    public function add_settings_page()
    {
        include SAAS_BASE . 'inc/admin/pages/settings-page.php';
    }

    public function add_schedule_edit_page()
    {
        if ( ! isset( $_GET['schedule_id'] ) ) {
            return;
        }
        global $current_screen;
        $schedule_arr = Saas::schedule()->get( sanitize_text_field( $_GET['schedule_id'] ) );
        if ( class_exists( 'Sabali_Option_Renderer' ) ) {
            $render = new Sabali_Option_Renderer();
        }
        if ( isset( $render ) && ! method_exists( $render, 'render_checkbox_text' ) ) {
            include SAAS_BASE . 'inc/admin/option-render-extended.php';
            $render_backup = new Sabali_Renderer();
        } else {
            include SAAS_BASE . 'inc/admin/option-render-extended.php';
            $render = new Sabali_Renderer();
        }
        wp_localize_script( 'saas-admin-js', 'saasTax', get_taxonomies() );
        $edit = true;
        echo '<div class="saas-vue-app">';
        include SAAS_BASE . 'inc/admin/pages/auto-share/new.php';
        echo '</div>';
    }
}