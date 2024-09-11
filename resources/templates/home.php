<?php

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
		<?php echo esc_html__('Posting Overview', 'nevamiss') ?>

    </h2>
    <div class="summary">
        <div class="upcoming-overview">
            <h3>
                <?php echo esc_html__('Upcoming posts', 'nevamiss') ?>
            </h3>

            <?php foreach ($posts as $post ): ?>
                <div class="schedule-overview-wrap" data-schedule-id="<?php esc_attr_e($post['id']); ?>">
                        <h4>
                            <?php esc_html_e($post['schedule_name']); ?>

                        </h4>
                    <i>
		                <?php echo esc_html__('You can drag posts to re-order them. Yes they will post in that order.', 'nevamiss') ?>
                    </i>
                        <div class="schedule-overview">
                            <ul class="item-list schedule-times">
                                <?php foreach ($post['posting_times'] as $posting_time): ?>
                                    <li class="schedule-time">
                                        <span> <?php esc_html_e($posting_time); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <ul class="item-list wp-posts-list">
                                <?php foreach ($post['posts'] as $post_item): ?>
                                    <li class="post-item post-item-<?php esc_attr_e($post_item[0]); ?>" data-schedule-post-id="<?php esc_attr_e($post_item[0]) ?>">
                                        <span> <?php esc_html_e($post_item[1]); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                </div>
            <?php endforeach; ?>

        </div>
        <div class="last-posted-overview">
            <h3>
                <?php echo esc_html__('Last posted', 'nevamiss') ?>
            </h3>
        </div>
    </div>
</div>
