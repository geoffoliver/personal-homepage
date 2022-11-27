<?php
if (!$hasFollowings) {
    echo $this->Html->div('box',
        implode('<br>', [
            __('You aren\'t following anybody!'),
            $this->Html->link(
                __('Add Following'),
                [
                    'controller' => 'Followings',
                    'action' => 'add'
                ]
            )
        ])
    );
    return;
}


if (count($posts) === 0) {
    if ($this->request->getQuery('page')) {
        echo $this->Html->div('box', __('There are no more posts to display.'));
    } else {
        echo $this->Html->div('box', __('There are no posts to display.'));
    }
    return;
}

foreach ($posts as $n => $post) {
    echo $this->element(
        'feed/item',
        ['post' => $post]
    );
}

if ($pagination['prev'] || $pagination['next']) {
    echo '<div class="load-next">';
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
