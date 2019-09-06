<?php
$Parsedown = new ParsedownExtra();
$this->assign('title', $post->title);
$this->append('css', $this->Html->css('posts/view.css'));
$this->append('script', $this->Html->script('posts/view.js'));
?>
<section class="section" id="viewPost">
    <article>
        <div class="post-title">
            <div class="columns">
                <div class="column is-10 is-offset-1">
                    <h1 class="is-size-2"><?= $post->title; ?></h1>
                    <div class="level is-mobile">
                        <h3 class="is-size-6 has-text-grey level-left">
                            <?= __('Posted'); ?>&nbsp;<time><?= $post->created->format('F j, Y \a\t g:i a'); ?></time>
                        </h3>
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
                    </div>
                </div>
            </div>
        </div>
        <div class="post-body">
            <div class="columns">
                <div class="column is-10 is-offset-1">
                    <div class="box">
                        <div class="post-content content">
                            <?= $Parsedown->text($post->content); ?>
                        </div>
                        <?php if ($post->medias): ?>
                        <div class="post-media">
                            <?php foreach ($post->medias as $media): ?>
                                <img
                                    src="/media/<?= $media->thumbnail; ?>"
                                    data-original="/media/<?= $media->local_filename; ?>"
                                    alt="<?= $media->name; ?>"
                                />
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </article>
    <?php if ($post->allow_comments): ?>
        <div class="comments" id="comments">
            <div class="columns">
                <div class="column is-10 is-offset-1">
                    <div class="comments-list">
                        <h3 class="is-size-5"><?= __('Comments ({0})', $this->Number->format(count($post->comments))); ?></h3>
                        <div class="content">
                            <?php if (count($post->comments) === 0): ?>
                                <div class="message is-info no-comments">
                                    <div class="message-body">
                                        <?= __('There are no comments to display.'); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <? foreach ($post->comments as $comment): ?>
                                <div class="comment">
                                    <div class="comment-info">
                                        <div class="commenter-name">
                                            <?= $comment->display_name; ?>
                                        </div>
                                        <time>
                                            <?= $comment->created; ?>
                                        </time>
                                    </div>
                                    <div class="comment-content">
                                        <?= nl2br(h($comment->comment)); ?>
                                    </div>
                                    <?php if ($this->Identity->isLoggedIn()): ?>
                                        <div class="level is-mobile">
                                            <div class="level-left"></div>
                                            <div class="level-right is-size-7">
                                                <?php if (!$comment->approved): ?>
                                                    <?= $this->Form->postLink(
                                                        '<span class="fas fa-check"></span>&nbsp;' . __('Approve'),
                                                        [
                                                            'controller' => 'Comments',
                                                            'action' => 'approve',
                                                            $comment->id
                                                        ],
                                                        [
                                                            'escape' => false,
                                                            'class' => 'level-item',
                                                            'confirm' => __('Are you sure you want to approve this comment?')
                                                        ]
                                                    ); ?>
                                                <?php endif; ?>
                                                <?= $this->Form->postLink(
                                                    '<span class="fas fa-trash"></span>&nbsp;' . __('Delete'),
                                                    [
                                                        'controller' => 'Comments',
                                                        'action' => 'delete',
                                                        $comment->id
                                                    ],
                                                    [
                                                        'escape' => false,
                                                        'class' => 'level-item',
                                                        'confirm' => __('Are you sure you want to delete this comment?')
                                                    ]
                                                ); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <? endforeach; ?>
                        </div>
                        <div class="comment-form">
                            <h4 class="is-size-5 has-text-grey-dark"><?= __('Leave A Comment'); ?></h4>
                            <p class="has-text-grey-dark">
                                <?= __('Use the form below to leave a comment on the post.'); ?>
                            </p>
                            <?= $this->Form->create(null, ['id' => 'commentsForm', 'url' => ['controller' => 'Comments', 'action' => 'add']]); ?>
                            <div class="columns">
                                <div class="column is-6">
                                    <?= $this->Form->control('display_name', [
                                        'type' => 'text',
                                        'label' => __('Your Name'),
                                        'placeholder' => __('Used in comment attribution'),
                                        'required' => true,
                                        'maxlength' => 255,
                                    ]); ?>
                                </div>
                                <div class="column is-6">
                                    <?= $this->Form->control('posted_by', [
                                        'type' => 'email',
                                        'label' => __('Email Address'),
                                        'placeholder' => __('Not displayed on site'),
                                        'required' => true,
                                        'maxlength' => 255,
                                    ]); ?>
                                </div>
                            </div>
                                    <?= $this->Form->control('comment', [
                                        'type' => 'textarea',
                                        'label' => __('Comment'),
                                        'required' => true,
                                    ]); ?>
                            <?php
                                echo $this->Form->button(
                                    __('Add Comment'),
                                    [
                                        'type' => 'submit',
                                        'class' => 'button is-link'
                                    ]
                                );

                                echo $this->Form->hidden('model_id', ['value' => $post->id]);
                            echo $this->Form->end();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>
