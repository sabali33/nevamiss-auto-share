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


	<div class="suggestion-form">
        <h2><?php esc_html_e('Suggestions/Feedback', 'nevamiss') ?></h2>
		<form action="" method="post">
            <?php echo wp_nonce_field('nevamiss-suggestion-form-action'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <p>
                <label for="full-name"><?php esc_html_e('Your name(<i>optional</i>)', 'nevamiss') ?></label>
                <input type="text" name="fullname" id="full-name">
            </p>
            <p>
                <label for="email-address"><?php esc_html_e('Email', 'nevamiss') ?></label>
                <input type="email" name="email_address" id="email-address" value="<?php echo esc_attr(get_option('admin_email')); ?>">
            </p>
            <div>
                <label for="suggestion"><?php esc_html_e('Your Suggestion:', 'nevamiss'); ?></label><br>
                <textarea id="suggestion" name="suggestion" rows="8" cols="100"></textarea><br>

            </div>
            <div>
                <input type="submit" class="button button-primary" value="<?php esc_html_e('Submit', 'nevamiss'); ?>">
            </div>


		</form>
	</div>
</div>
