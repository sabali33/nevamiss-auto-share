<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Post_Meta;

class Post_Meta {

    public function meta_boxes()
    {
        $allowed_post_types = ['post'];

        add_meta_box('nevamiss-auto-share', __('Auto Share', 'nevamiss'), [$this, 'show_meta_box'], [$allowed_post_types]);
    }

    public function show_meta_box()
    {
        echo "Welcome to sharing instantly";
    }
}