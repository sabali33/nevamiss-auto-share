<?php

use Nevamiss\Presentation\Components\Input_Fields\Input;
use Nevamiss\Presentation\Pages\Schedule_Form;

/**
 * @var Schedule_Form $this
 */

?>

<h1 class="wp-heading-inline">
    <?php echo esc_html($this->title); ?>
</h1>

<?php
if($this->schedule()){
    $this->update_form();
    $button_label = __('Update', 'nevamiss');
}else{
	$this->maybe_save_form();
	$button_label = __('Create', 'nevamiss');
}

?>

<div class="wrap schedule-form">
    <form method="post">
        <?php wp_nonce_field('nevamiss_create_schedule'); ?>
        <?php foreach ($this->fields() as $field): ?>
            <?php echo $this->render_field($field)->render(); ?>
        <?php endforeach; ?>
        <input type="submit" value="<?php esc_attr_e($button_label); ?>">
    </form>
</div>