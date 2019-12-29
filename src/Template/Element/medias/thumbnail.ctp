<?php

    if (strpos($media->mime, 'audio/') === 0) {
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
        echo $this->Html->link(
            $this->Html->tag(
                'img',
                null,
                [
                    'data-lazy-src' => $this->Url->build([
                        'controller' => 'Medias',
                        'action' => 'download',
                        $media->id,
                        isset($size) ? $size : 'thumbnail'
                    ])
                ]
            ),
            [
                'controller' => 'Medias',
                'action' => 'view',
                $media->id
            ],
            [
                'escape' => false
            ]
        );
    }
