<div class="media-container">
    <?php
        if (strpos($media->mime, 'video/') === 0) {
            echo $this->Html->div('video-container',
                $this->Html->media(
                    "/media/{$media->local_filename}",
                    [
                        'fullBase' => true,
                        'text' => $media->name . $media->description  ? (' - ' . $media->description) : '',
                        'controls' => true
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
