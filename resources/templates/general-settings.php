<?php

declare(strict_types=1);

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Tabs\General_Tab;

/**
 * @var General_Tab $this
 */

$current_section = $_GET['section']?? 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

?>

<ul class="subsubsub">
	<?php
	/**
	 * @var Component $section_tab
	 */
	try {
		foreach ($this->section_tabs($current_section) as $section_tab) {
			echo "<li>{$section_tab->render()}</li>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	} catch (Not_Found_Exception $e) {
        $this->redirect([
                'status' => 'error',
            'message' => $e->getMessage(),
        ]);
	}
	?>
</ul>
<div class="clear"></div>
<div class="tab-section">
    <form action="<?php echo esc_url(admin_url('admin-post.php?action=nevamiss_settings')) ?>" method="post">
        <input type="hidden" name="page" value="nevamiss-settings">
        <input type="hidden" name="tab" value="general">
        <input type="hidden" name="section" value="<?php echo esc_attr($current_section); ?>">
        <input type="hidden" name="action" value="nevamiss_settings">
        <?php wp_nonce_field('nevamiss-general-settings-action') ?>
    <?php
    /**
     * @var Component $field
     */
        foreach ($this->render_sections($current_section) as $field){
            echo $field->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    ?>
        <input type="submit" class="button button-primary" value="<?php esc_attr_e('Save', 'nevamiss'); ?>">
    </form>
</div>