<?= $this->Html->tag(
    'img',
    null,
    [
        'data-lazy-src' => $this->Url->build([
            'controller' => 'Medias',
            'action' => 'download',
            $media->id,
            'square_thumbnail'
        ]),
        'loading' => 'lazy',
    ]
);
