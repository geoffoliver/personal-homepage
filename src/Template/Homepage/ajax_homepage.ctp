<?php
if (count($posts) === 0) {
    echo $this->Html->div('box', implode('', [
        $this->Html->tag('h4', __('There is nothing to show here.'), [
            'class' => 'title is-6'
        ]),
        $this->Html->para('', __('If you are the site owner, you should add your first post!'))
    ]));
    return;
}

foreach ($posts as $post) {
    echo $this->element('homepage/post', ['post' => $post]);
}

if ($this->Paginator->hasNext()) {

    $nextPage = (int)$this->request->getQuery('page', 1) + 1;

    $url = "/?page={$nextPage}";

    if ($authed) {
        $url = "/homepage?page={$nextPage}";
    }

    echo $this->Html->div('load-next',
        $this->Html->link(
            __('Older Posts'),
            $url,
            [
                'class' => 'paginate button is-dark is-fullwidth',
                'data-page' => $nextPage
            ]
        )
    );
}
