<div class="media-container">
    <?php
        if (strpos($media->mime, 'video/') === 0) {
            echo $this->Html->div('video-container',
                $this->Html->media(
                    $this->Url->build([
                        'controller' => 'Medias',
                        'action' => 'download',
                        $media->id
                    ]),
                    [
                        'fullBase' => true,
                        'text' => $media->name . $media->description  ? (' - ' . $media->description) : '',
                        'controls' => true,
                        'tag' => 'video',
                        'autoplay' => false
                    ]
                )
            );
        } elseif (strpos($media->mime, 'audio/') === 0){
            echo $this->Html->div('audio-container',
                $this->Html->media(
                    $this->Url->build([
                        'controller' => 'Medias',
                        'action' => 'download',
                        $media->id
                    ]),
                    [
                        'fullBase' => true,
                        'controls' => true,
                        'tag' => 'audio',
                        'autoplay' => false
                    ]
                )
            );

        } else {
            echo $this->Html->tag('img', null, [
                'data-lazy-src' => $this->Url->build([
                    'controller' => 'Medias',
                    'action' => 'download',
                    $media->id,
                ])
            ]);
        }
    ?>
</div>
