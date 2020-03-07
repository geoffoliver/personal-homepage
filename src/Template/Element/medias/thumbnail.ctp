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
    } elseif (strpos($media->mime, 'video/') === 0) {
        echo $this->Html->div('video-container',
            $this->Html->tag('video', null, [
                'controls' => true,
                'loop' => true,
                'data-lazy-src' => $this->Url->build([
                    'controller' => 'Medias',
                    'action' => 'download',
                    $media->id
                ]),
                'loading' => 'lazy',
            ])
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
                    ]),
                    'loading' => 'lazy',
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
