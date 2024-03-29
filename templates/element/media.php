<?php
    $render = "";

    if (strpos($media->mime, 'video/') === 0) {
        $render = $this->Html->div('video-container',
            $this->Html->tag('video', null, [
                'controls' => true,
                'loop' => true,
                'loading' => 'lazy',
                'class' => 'u-video',
                'src' => $this->Url->build([
                    'controller' => 'Medias',
                    'action' => 'download',
                    $media->id
                ], [
                    'fullBase' => true
                ]),
            ])
            /*
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
            */
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
                    'autoplay' => false,
                    'class' => 'u-audio',
                ]
            )
        );
    } elseif (strpos($media->mime, 'image/') === 0) {
        $render = $this->Html->tag('img', null, [
            'class' => 'u-photo',
            'loading' => 'lazy',
            'src' => $this->Url->build([
                'controller' => 'Medias',
                'action' => 'download',
                $media->id,
            ], [
                'fullBase' => true
            ]),
        ]);
    } else {
        $render = $this->Html->link(
            $media->name,
            $this->Url->build([
                'controller' => 'Medias',
                'action' => 'download',
                $media->id,
            ], [
                'fullBase' => true
            ]),
        );
    }

    echo $this->Html->div('media-container', $render);
