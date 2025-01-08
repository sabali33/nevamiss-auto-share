<?php

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Nevamiss\Presentation\Post_Meta\Post_Meta;


/**
 * @var Post_Meta $this
 * @var WP_Post $post
 */

?>
<?php
$accounts = $this->accounts();
if(empty($accounts)){
	$logins_page = admin_url('admin.php?page=nevamiss-settings&tab=network-accounts');

	printf(
        wp_kses(
        /* translators: %s: A link to the login page */
            __('No Accounts created yet. <a href="%s">Login to create one</a>', 'nevamiss'),
            [
                'a' => array(
                    'href' => array()
                )
            ]
        ),
        esc_url($logins_page)
    );
}

?>
<ul class="nevamiss-instant-share-list">
<?php

    foreach($accounts as $account){
        $url = esc_url(add_query_arg(
            [
                'action' => 'nevamiss_instant_share',
                'post_id' => $post->ID,
                'account_id' => $account->id(),
                'nonce' => wp_create_nonce('nevamiss-instant-share-action')
            ],
            admin_url('admin-ajax.php')
        ));
	    /* translators: %1$s: Account name %2$s: Network */
        $title = sprintf(esc_html__('Share to this post %1$s(%2$s)', 'nevamiss'),  $account->name(), $account->network());

        echo wp_kses_post("<li> <a href='$url' title='$title' class='nevamiss-instant-share-link'>$title</a></li>");
    }

?>
</ul>
