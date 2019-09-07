<?php

$this->assign('title', $media->name);
$this->append('css', $this->Html->css('medias/view.css'));
/*
$this->append('script', $this->Html->script('posts/view.js'));
*/
?>
<section class="section" id="viewMedia">
    <article>
        <div class="media-title">
            <div class="columns">
                <div class="column is-10 is-offset-1">
                    <h1 class="is-size-2"><?= $media->name; ?></h1>
                </div>
            </div>
        </div>
        <div class="media-body">
            <div class="columns">
                <div class="column is-10 is-offset-1">
                    <div class="box">
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
                        <?php if($media->description): ?>
                            <div class="media-description">
                                <hr />
                                <?= nl2br(h($media->description)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?= $this->element('comments', ['post' => $media]); ?>
    </div>
</section>
