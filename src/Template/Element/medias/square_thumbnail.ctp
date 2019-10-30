<?= $this->Html->image(
    null,
    [
        'data-lazy-src' => $this->Url->build([
            'controller' => 'Medias',
            'action' => 'download',
            $media->id,
            'square_thumbnail'
        ])
    ]
);
