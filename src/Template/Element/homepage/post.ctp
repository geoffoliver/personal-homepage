<?php
$Parsedown = new ParsedownExtra();
?>
<div class="box homepage-post">
    <article class="media">
        <div class="media-content">
        <div class="content">
            <h1 class="is-marginless is-size-4">
                <?= $this->Html->link(
                    $post->title,
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
                        <?php if ($media->thumbnail): ?>
                            <?= $this->Html->link(
                                $this->Html->image(
                                    "/media/{$media->thumbnail}"
                                ),
                                [
                                    'controller' => 'Medias',
                                    'action' => 'view',
                                    $media->id
                                ],
                                [
                                    'escape' => false
                                ]
                            ); ?>
                        <?php else: ?>
                        ...
                        <?php endif;?>
                    <?php endforeach;?>
                </div>
            <?php endif;?>
            <hr />
            <nav class="level is-mobile is-size-7">
                <div class="level-left">
                    <?= $this->Html->link(
                        '<span class="fas fa-comment" aria-hidden="true"></span>&nbsp' . __('{0} Comments', count($post->comments)),
                        [
                            '_name' => 'viewPost',
                            $post->id . '#comments',
                        ],
                        [
                            'escape' => false,
                            'class' => 'level-item'
                        ]
                    ); ?>
                    <a class="level-item" aria-label="retweet">
                        <span class="icon is-small">
                            <span class="fas fa-share" aria-hidden="true"></span>
                        </span>
                        &nbsp;
                        <?= __('Share'); ?>
                    </a>
                    <?php
                        if ($post->source) {
                            echo $this->Html->link(
                                '<span class="fas fa-external-link-alt" aria-hidden="true"></span>&nbsp' . __('View Original'),
                                $post->source,
                                [
                                    'escape' => false,
                                    'class' => 'level-item',
                                    'target' => '_blank'
                                ]
                            );
                        }
                    ?>
                </div>
                <?php if ($this->Identity->isLoggedIn()) :?>
                    <div class="level-right">
                        <?= $this->Html->link(
                            '<span class="fas fa-edit" aria-hidden="true"></span>&nbsp;' . __('Edit'),
                            [
                                'controller' => 'Posts',
                                'action' => 'edit',
                                $post->id
                            ],
                            [
                                'class' => 'level-item',
                                'escape' => false
                            ]
                        ); ?>
                        <?= $this->Form->postLink(
                            '<span class="fas fa-trash" aria-hidden="true"></span>&nbsp;' . __('Delete'),
                            [
                                'controller' => 'Posts',
                                'action' => 'delete',
                                $post->id
                            ],
                            [
                                'confirm' => __('Are you sure you want to delete this post?'),
                                'escape' => false
                            ]
                        ); ?>
                    </div>
                <?php endif; ?>
            </nav>
        </div>
        </div>
    </article>
</div>
