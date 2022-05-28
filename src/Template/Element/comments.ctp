<?php
use Cake\Utility\Hash;
// let's figure out how many comments this thing has because we may or may not
// actually display a list of comments and a form depending on that. the logic
// here is, since you can enable/disable comments for something, and there may
// be comments to display, that we should still display any existing comments
// while preventing new comments if that's how this thing has been configured.
$totalComments = count($post->comments);

// try to get values for the 'name' and 'email' fields from the cookies
$name = isset($_COOKIE['comment_name']) ? $_COOKIE['comment_name'] : '';
$email = isset($_COOKIE['comment_email']) ? $_COOKIE['comment_email'] : '';
?>
<div class="comments" id="comments">
    <div class="comments-list">
        <?php if ($post->allow_comments || (!$post->allow_comments && $totalComments > 0)): ?>
            <h3 class="subtitle"><?= __('Comments ({0})', $this->Number->format($totalComments)); ?></h3>
            <?php if (count($post->comments) === 0): ?>
                <div class="message is-dark no-comments">
                    <div class="message-body is-size-7">
                        <?= __('There are no comments to display.'); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($post->comments): ?>
            <div class="comments-container">
                <?php foreach ($post->comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-info">
                            <div class="commenter-name">
                                <?= $comment->display_name; ?>
                            </div>
                            <time>
                                <?= $comment->created->setTimezone(Hash::get($settings, 'timezone'))->format(Hash::get($settings, 'time-format')); ?>
                            </time>
                        </div>
                        <div class="comment-content">
                            <?= nl2br(h($comment->comment)); ?>
                        </div>
                        <?php if ($this->Identity->isLoggedIn()): ?>
                            <div class="level is-mobile">
                                <div class="level-left is-size-7">
                                    <?php
                                        if (!$comment->approved) {
                                            echo $this->Form->postLink(
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
                                            );
                                        }
                                        echo $this->Form->postLink(
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
                                        );
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php if ($post->allow_comments): ?>
                <div class="comment-form">
                    <h4 class="is-size-6 has-text-grey-dark">
                        <a onClick="toggleCommentForm(this)" href="javascript:void(0);">
                            <span class="fas fa-comment"></span>
                            <?= __('Leave A Comment'); ?>
                        </a>
                    </h4>
                    <?php
                        echo $this->Form->create(null, [
                            'id' => 'commentsForm',
                            'url' => ['controller' => 'Comments', 'action' => 'add'],
                            'style' => 'display: none;'
                        ]);

                        echo $this->Form->control('display_name', [
                            'type' => 'text',
                            'label' => __('Your Name'),
                            'placeholder' => __('Used in comment attribution'),
                            'required' => true,
                            'maxlength' => 255,
                            'value' => $name
                        ]);

                        echo $this->Form->control('posted_by', [
                            'type' => 'email',
                            'label' => __('Email Address'),
                            'placeholder' => __('Not displayed on site'),
                            'required' => true,
                            'maxlength' => 255,
                            'value' => $email
                        ]);

                        echo $this->Form->control('comment', [
                            'type' => 'textarea',
                            'label' => __('Comment'),
                            'required' => true,
                        ]);

                        echo $this->Form->button(
                            __('Add Comment'),
                            [
                                'type' => 'submit',
                                'class' => 'button is-dark'
                            ]
                        );

                        echo $this->Form->hidden('model_id', ['value' => $post->id]);

                        echo $this->Form->end();
                    ?>
                </div>
            <?php endif; ?>
        <?php elseif (!$post->allow_comments): ?>
            <h2 class="is-size-5"><?= __('Comments'); ?></h2>
            <div class="message is-dark no-comments">
                <div class="message-body is-size-7">
                    <?= __('Commenting has been disabled.'); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
function toggleCommentForm(element) {
    var form = element.parentNode.nextElementSibling;
    form.style.display = (form.style.display == 'none' ? 'block' : 'none');
}
</script>
