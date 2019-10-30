<?php
$Parsedown = new ParsedownExtra();
?>
<div class="box homepage-post">
    <article class="media">
        <div class="media-content">
            <div class="content">
                <h1 class="is-marginless is-size-4">
                    <?= $this->Html->link(
                        $post->name,
                        ['_name' => 'viewPost', $post->id]
                    ); ?>
                </h1>
                <h5 class="is-size-7 has-text-grey-light">
                    <?php if ($post->import_source): ?>
                        <span class="fab fa-<?= $post->import_source; ?>" aria-hidden="true"></span>
                    <?php endif; ?>
                    <?= $post->created->setTimezone('America/Denver')->format('F j, Y \a\t g:i a'); ?>
                    <?php if ($post->created != $post->modified): ?>
                        &middot; <?= __('Updated'); ?>&nbsp;<?= $post->modified->format('F j, Y \a\t g:i a'); ?>
                    <?php endif; ?>
                </h5>
                <p>
                    <?= $Parsedown->text($post->content); ?>
                </p>
                <?php if ($post->medias): ?>
                    <div class="post-media">
                        <?php foreach ($post->medias as $media): ?>
                            <?= $this->Html->link(
                                $this->element('medias/thumbnail', ['media' => $media]),
                                [
                                    'controller' => 'Medias',
                                    'action' => 'view',
                                    $media->id
                                ],
                                [
                                    'escape' => false
                                ]
                            ); ?>
                        <?php endforeach;?>
                    </div>
                <?php endif;?>
                <hr />
                <?= $this->element('item-footer', ['item' => $post]); ?>
            </div>
        </div>
    </article>
</div>
