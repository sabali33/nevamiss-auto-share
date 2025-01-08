<?php

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Nevamiss\Presentation\Components\Input_Fields\Input;
use Nevamiss\Presentation\Pages\Schedule_Form;

/**
 * @var Schedule_Form $this
 */

?>

<?php

$button_label = $this->schedule() ? __('Update', 'nevamiss') : __('Create', 'nevamiss');
$schedule_id = $this->schedule() ? $this->schedule()->id() : null;

?>

<div class="wrap schedule-form">
    <?php $this->notices(); ?>

    <h1 class="wp-heading-inline">
		<?php echo esc_html($this->title()); ?>
    </h1>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=nevamiss_create_schedule')); ?>">
        <?php wp_nonce_field('nevamiss_create_schedule'); ?>
        <input type="hidden" name="action" value="nevamiss_create_schedule">
        <?php if($schedule_id): ?>
            <input type="hidden" name="schedule_id" value="<?php echo esc_attr($this->schedule()->id()); ?>">
        <?php endif; ?>

        <?php foreach ($this->fields() as $field): ?>
            <?php echo wp_kses_post($this->render_field($field)->render());  ?>
        <?php endforeach; ?>

        <input type="submit" class="button button-primary" value="<?php echo esc_attr($button_label); ?>">
    </form>
</div>