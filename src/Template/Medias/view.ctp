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
                        <?= $this->element('media', ['media' => $media]); ?>
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
