<?php

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Nevamiss\Presentation\Pages\Auto_Share_Page;

/**
 * @var Auto_Share_Page $this
 */

try {
	$posts = $this->upcoming_posts();

}catch (Throwable $throwable ){
	echo esc_html($throwable->getMessage());
	return;
}
?>

<div class="wrap post-overview">
    <h2>
		<?php esc_html_e('Posting Overview', 'nevamiss') ?>

    </h2>
    <div class="summary">
        <div class="upcoming-overview">
            <h3>
                <?php esc_html_e('Upcoming posts', 'nevamiss') ?>
            </h3>

            <?php foreach ($posts as $post ): ?>
                <div class="schedule-overview-wrap" data-schedule-id="<?php echo esc_attr($post['id']); ?>">
                        <h4>
                            <?php echo esc_html($post['schedule_name']); ?>

                        </h4>
                    <i>
		                <?php esc_html_e('You can drag posts to re-order them. Yes they will post in that order.', 'nevamiss') ?>
                    </i>
                        <div class="schedule-overview">
                            <ul class="item-list schedule-times">
                                <?php foreach ($post['posting_times'] as $posting_time): ?>
                                    <li class="schedule-time">
                                        <span> <?php echo esc_html($posting_time); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <ul class="item-list wp-posts-list">
                                <?php foreach ($post['posts'] as $post_item): ?>
                                    <li class="post-item post-item-<?php echo esc_attr($post_item[0]); ?>" data-schedule-post-id="<?php echo esc_attr($post_item[0]) ?>">
                                        <span> <?php echo esc_html($post_item[1]); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                </div>
            <?php endforeach; ?>

        </div>
        <div class="last-posted-overview">
            <h3>
                <?php esc_html_e('Last posted', 'nevamiss') ?>
            </h3>
            <ul>
                <?php foreach ($this->last_posted() as $schedule_posts): ?>
                <li>
                    <h4>
                        <?php echo esc_html($schedule_posts['post'][1]); ?>
                    </h4>

                    <span>
                        <?php
                            /* translators: %1$s: Schedule name %2$s: Time posted to network */
                            echo sprintf(
                                '<i>%3$s</i> %1$s Schedule @ %2$s',
                                esc_html($schedule_posts['schedule_name']),
                                esc_html($schedule_posts['posted_on']),
                                esc_html__('Posted by', 'nevamiss')
                            )
                        ?>
                    </span>

                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
