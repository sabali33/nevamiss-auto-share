<?php

use Nevamiss\Presentation\Components\Input_Fields\Input;
use Nevamiss\Presentation\Pages\Schedule_Form;

/**
 * @var Schedule_Form $this
 */

?>

<?php

$button_label = $this->schedule() ? __('Update', 'nevamiss') : __('Create', 'nevamiss');

if($this->schedule()){
	$this->update_form();
}else{
	$this->maybe_save_form();
}
?>

<div class="wrap schedule-form">
    <?php $this->notices(); ?>

    <h1 class="wp-heading-inline">
		<?php echo esc_html($this->title()); ?>
    </h1>
    <form method="post">
        <?php wp_nonce_field('nevamiss_create_schedule'); ?>
        <?php foreach ($this->fields() as $field): ?>
            <?php echo $this->render_field($field)->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php endforeach; ?>
        <input type="submit" class="button button-primary" value="<?php echo esc_attr($button_label); ?>">
    </form>
</div>