<?php

declare(strict_types=1);

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Tabs\General_Tab;

/**
 * @var General_Tab $this
 */

$current_section = $_GET['section']?? 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$this->maybe_save_settings();
?>

<ul class="subsubsub">
	<?php
	/**
	 * @var Component $section_tab
	 */
	try {
		foreach ($this->section_tabs($current_section) as $section_tab) {
			echo "<li>{$section_tab->render()}</li>";
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
    <form action="" method="post">
        <input type="hidden" name="page" value="nevamiss-settings">
        <input type="hidden" name="tab" value="general">
        <input type="hidden" name="section" value="<?php esc_attr_e($current_section); ?>">
        <?php wp_nonce_field('nevamiss-general-settings-action') ?>
    <?php
    /**
     * @var Component $field
     */
        foreach ($this->render_sections($current_section) as $field){
            echo $field->render();
        }
    ?>
        <input type="submit" class="button button-primary" value="<?php esc_attr_e('Save', 'nevamiss'); ?>">
    </form>
</div>