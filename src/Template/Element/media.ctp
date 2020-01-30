<?php
    $render = "";

    if (strpos($media->mime, 'video/') === 0) {
        $render = $this->Html->div('video-container',
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
        $render = $this->Html->div('audio-container',
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
    } elseif (strpos($media->mime, 'image/') === 0) {
        $render = $this->Html->tag('img', null, [
            'data-lazy-src' => $this->Url->build([
                'controller' => 'Medias',
                'action' => 'download',
                $media->id,
            ])
        ]);
    } else {
        $render = $this->Html->link(
            $media->name,
            $this->Url->build([
                'controller' => 'Medias',
                'action' => 'download',
                $media->id,
            ])
        );
    }

    echo $this->Html->div('media-container', $render);
