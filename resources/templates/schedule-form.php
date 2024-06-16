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

<?php $this->maybe_save_form(); ?>

<div class="wrap">
    <form method="post">
        <?php wp_nonce_field('nevamiss_create_schedule'); ?>
        <?php foreach ($this->fields() as $field): ?>
            <p> <?php $this->render_field($field); ?> </p>
        <?php endforeach; ?>
        <input type="submit" value="<?php esc_attr_e('Create', 'nevamiss'); ?>">
    </form>
</div>