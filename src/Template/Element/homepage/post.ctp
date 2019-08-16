<div class="box">
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
                <?= $post->created->format('F j, Y \a\t g:i a'); ?>
                <?php if ($post->created != $post->modified): ?>
                    &middot; <?= __('Updated'); ?>&nbsp;<?= $post->modified->format('F j, Y \a\t g:i a'); ?>
                <?php endif; ?>
            </h5>
            <p>
                <?= $post->content; ?>
            </p>
            <?php if ($post->medias): ?>
                <?php foreach ($post->medias as $media): ?>
                    <?php if ($media->thumbnail): ?>
                        <img src="/media/<?=$media->thumbnail;?>" />
                    <?php else: ?>
                    ...
                    <?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
            <hr />
            <nav class="level is-mobile">
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
