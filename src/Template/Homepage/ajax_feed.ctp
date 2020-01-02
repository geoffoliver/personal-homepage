<?php
if (count($posts) === 0) {
    if ($this->request->getQuery('page')) {
        echo $this->Html->div('box', __('There are no more posts to display.'));
    } else {
        echo $this->Html->div('box', __('There are no posts to display.'));
    }
    return;
}

foreach ($posts as $post) {
    echo $this->element(
        'feed/item',
        ['post' => $post]/*,
        ['cache' => ['key' => md5($post->id), 'config' => 'elements']]*/
    );
}

if ($pagination['prev'] || $pagination['next']) {
    echo '<div class="load-next">';
        /*
        if ($this->request->is('ajax') && $pagination['prev']) {
            echo $this->Html->div('load-prev',
                $this->Html->link(
                    __('Newer Posts'),
                    "/?page={$pagination['prev']}",
                    [
                        'class' => 'paginate button is-dark is-full-width',
                        'data-page' => $pagination['prev']
                    ]
                )
            );
        }
        */
        if ($pagination['next']) {
            echo $this->Html->div('load-next',
                $this->Html->link(
                    __('Older Posts'),
                    "/?page={$pagination['next']}",
                    [
                        'class' => 'paginate button is-dark is-fullwidth',
                        'data-page' => $pagination['next']
                    ]
                )
            );
        }
    echo '</div>';
}
