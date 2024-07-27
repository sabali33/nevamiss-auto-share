<?php

declare(strict_types=1);

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
	printf(__('No Accounts created yet. <a href="%s">Login to create one</a>', 'nevamiss'), $logins_page);
}

?>
<ul class="nevamiss-instant-share-list">
<?php

    foreach($accounts as $account){
        $url = add_query_arg(
            [
                'action' => 'nevamiss_instant_share',
                'post_id' => $post->ID,
                'account_id' => $account->id(),
                'nonce' => wp_create_nonce('nevamiss-instant-share-action')
            ],
            admin_url('admin-ajax.php')
        );

        $title = sprintf(__('Share to this post %s(%s)', 'nevamiss'),  $account->name(), $account->network());

        echo "<li> <a href='$url' title='$title' class='nevamiss-instant-share-link'>$title</a></li>";
    }

?>
</ul>
