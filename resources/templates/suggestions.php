<?php

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Nevamiss\Presentation\Pages\Suggestions_Page;

/**
 * @var Suggestions_Page $this
 */

?>

<div class="wrap">
    <?php $this->notices(); ?>


	<div class="suggestion-form">
        <h2><?php esc_html_e('Suggestions/Feedback', 'nevamiss') ?></h2>
		<form action="<?php echo esc_url(admin_url('admin-post.php?action=nevamiss_suggestion_post')); ?>" method="post">
            <?php echo wp_kses_post(wp_nonce_field('nevamiss-suggestion-form-action')); ?>

            <p>
                <label for="full-name"><?php printf('%s(<i>%s</i>)', esc_html__('Your name','nevamiss'), esc_html__('Optional','nevamiss')); ?></label>
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
