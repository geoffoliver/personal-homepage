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
                        'autoplay' => true
                    ]
                )
            );
        } else {
            echo $this->Html->image(
                "/media/{$media->local_filename}"
            );
        }
    ?>
</div>
