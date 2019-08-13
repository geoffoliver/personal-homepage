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

    if ($pagination['prev'] || $pagination['next']) {
        echo '<div class="feed-pagination">';
            /*
            if ($this->request->is('ajax') && $pagination['prev']) {
                echo $this->Html->div('load-prev',
                    $this->Html->link(
                        __('Previous Page'),
                        "/?page={$pagination['prev']}",
                        [
                            'class' => 'paginate button is-link is-full-width',
                            'data-page' => $pagination['prev']
                        ]
                    )
                );
            }
            */

            if ($pagination['next']) {
                echo $this->Html->div('load-next',
                    $this->Html->link(
                        __('Next Page'),
                        "/?page={$pagination['next']}",
                        [
                            'class' => 'paginate button is-link is-full-width',
                            'data-page' => $pagination['next']
                        ]
                    )
                );
            }

        echo '</div>';
    }
}
