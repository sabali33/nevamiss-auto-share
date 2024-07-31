<?php

declare(strict_types=1);

use Nevamiss\Presentation\Pages\Suggestions_Page;

/**
 * @var Suggestions_Page $this
 */

$this->maybe_process_form();
?>

<div class="wrap">
    <?php $this->notices(); ?>

	<h2><?php _e('Suggestions/Feedback', 'nevamiss') ?></h2>
	<div class="suggestion-form">
		<form action="" method="post">
            <?php echo wp_nonce_field('nevamiss-suggestion-form-action'); ?>
            <p>
                <label for="full-name"><?php _e('Your name(<i>optional</i>)', 'nevamiss') ?></label>
                <input type="text" name="fullname" id="full-name">
            </p>
            <p>
                <label for="email-address"><?php _e('Email', 'nevamiss') ?></label>
                <input type="email" name="email_address" id="email-address" value="<?php esc_attr_e(get_option('admin_email')); ?>">
            </p>
            <div>
                <label for="suggestion"><?php _e('Your Suggestion:', 'nevamiss'); ?></label><br>
                <textarea id="suggestion" name="suggestion" rows="8" cols="100"></textarea><br>

            </div>
            <div>
                <input type="submit" class="button button-primary" value="<?php _e('Submit', 'nevamiss'); ?>">
            </div>


		</form>
	</div>
</div>
