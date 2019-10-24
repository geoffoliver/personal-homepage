<?php
$this->assign('title', $media->name);
$this->append('css', $this->Html->css('medias/view.css'));
$Parsedown = new ParsedownExtra();
?>
<section class="section" id="viewMedia">
    <article>
        <div class="media-title">
            <div class="columns">
                <div class="column is-10 is-offset-1">
                    <?php if ($media->name): ?>
                        <h1 class="is-size-2"><?= $media->name; ?></h1>
                    <?php endif; ?>
                    <?php if ($media->album): ?>
                        <h2 class="is-size-5">
                            <?= $this->Html->link(
                                __('View Album'),
                                [
                                    '_name' => 'viewAlbum',
                                    $media->album->id
                                ]
                            ); ?>
                        </h2>
                    <?php endif; ?>
                    <div class="level is-mobile">
                        <h3 class="is-size-6 has-text-grey level-left">
                            <?= __('Posted'); ?>&nbsp;<time><?= $media->created->format('F j, Y \a\t g:i a'); ?></time>
                        </h3>
                    </div>
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
                        <?php if ($media->post && $media->post->content): ?>
                            <hr />
                            <p>
                                <?= $Parsedown->text($media->post->content); ?>
                            </p>
                        <?php endif; ?>
                        <hr />
                        <?= $this->element('item-footer', ['item' => $media, 'comments' => false]); ?>
                    </div>
                </div>
            </div>
        </div>
        <?= $this->element('comments', ['post' => $media]); ?>
    </div>
</section>
