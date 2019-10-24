<div class="box">
    <div class="feed-post-item">
        <div class="feed-post-item-header">
            <figure class="image is-48x48">
                <?= $this->Html->link(
                    $this->Html->image(
                        $post->friend->icon,
                        ['class' => 'is-rounded friend-icon']
                    ),
                    $post->friend->url,
                    [
                        'target' => '_blank',
                        'escape' => false
                    ]
                ); ?>
            </figure>
            <div class="feed-post-item-friend-name-container">
                <h1 class="is-size-5 feed-post-item-title">
                    <?= $this->Html->link(
                        $post->title,
                        $post->url,
                        [
                            'target' => '_blank',
                            'rel' => 'noopener noreferrer'
                        ]
                    ); ?>
                </h1>
                <div class="feed-post-item-friend-name-and-time">
                    <?= $this->Html->link(
                        $post->friend->name,
                        $post->friend->url,
                        [
                            'target' => '_blank',
                            'class' => 'feed-post-item-friend-name'
                        ]
                    ); ?>
                    <?php if (isset($post->author) && isset($post->author->name) && $post->author->name): ?>
                        <span>
                        &mdash; by <?= $post->author->name; ?>
                        </span>
                    <?php endif; ?>
                    &middot;
                    <time>
                        <?= $post->date_published->nice('America/Denver'); ?>
                        <?php if ($post->date_published != $post->date_modified): ?>
                            <span class="updated-time">
                                (Updated <?= $post->date_modified->nice('America/Denver'); ?>)
                            </span>
                        <?php endif; ?>
                    </time>
                </div>
            </div>
        </div>
        <div class="feed-post-item-content">
            <div><?= $post->summary; ?></div>
        </div>
        <hr />
        <div class="feed-post-item-footer">
            <nav class="level is-mobile is-size-7">
                <div class="level-left">
                    <?= $this->Html->link(
                        $this->Html->tag('i', '', ['class' => 'fas fa-share']) . '&nbsp;' . __('Share'),
                        '#',
                        [
                            'escape' => false,
                            'target' => '_blank',
                            'rel' => 'noopener noreferrer',
                            'class' => 'level-item'
                        ]
                    ); ?>
                    <?= $this->Html->link(
                        $this->Html->tag('i', '', ['class' => 'fas fa-external-link-alt']) . '&nbsp;' . __('View Original'),
                        $post->url,
                        [
                            'escape' => false,
                            'target' => '_blank',
                            'rel' => 'noopener noreferrer',
                            'class' => 'level-item'
                        ]
                    ); ?>
                    <?php if (
                        isset($post->_page_feed) &&
                        $post->_page_feed->comments &&
                        $post->_page_feed->comments->url
                    ): ?>
                        <?= $this->Html->link(
                            $this->Html->tag('i', '', ['class' => 'fas fa-comment']) . '&nbsp;' . __('{0} Comments', $post->_page_feed->comments->total),
                            $post->_page_feed->comments->url,
                            [
                                'title' => 'View Comments',
                                'escape' => false,
                                'target' => '_blank',
                                'rel' => 'noopener noreferrer',
                                'class' => 'level-item'
                            ]
                        ); ?>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>
</div>
