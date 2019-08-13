<?php

if (count($posts) === 0) {
    echo $this->Html->div('box', __('There are no posts to display'));
} else {
    foreach ($posts as $post) {
        echo $this->element(
            'feed/item',
            ['post' => $post],
            ['cache' => ['key' => $post->id]]
        );
    }
}
