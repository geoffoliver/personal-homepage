<?php
use Cake\Utility\Hash;

$this->assign('title', __('Unapproved Comments'));
$this->append('css', $this->Html->css('comments/unapproved.css'));
$this->append('script', $this->Html->script('comments/unapproved.js'));
?>
<div id="unapproved-comments">
    <h1><?= __('Unapproved Comments ({0})', number_format($comments->count())); ?></h1>
    <div>
    <?php
        if ($comments->count() === 0) {
            echo $this->Html->div('message is-success', $this->Html->div('message-body', __('There are no unapproved comments.')));
        } else {
            echo $this->Form->create(null, ['id' => 'unapprovedComments']);
    ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="check-all" /></th>
                        <th>Date</th>
                        <th>Posted By</th>
                        <th>Comment</th>
                        <th>Commented On</th>
                        <th>
                            <ul class="actions">
                                <li>
                                    <a id="approve-checked" class="has-text-success" title="<?= __('Approve Checked'); ?>">
                                        <span class="fas fa-check"></span>
                                    </a>
                                </li>
                                <li>
                                    <a id="delete-checked" class="has-text-danger" title="<?= __('Delete Checked'); ?>">
                                        <span class="fas fa-trash"></span>
                                    </a>
                                </li>
                            </ul>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="comment[]" value="<?= $comment->id; ?>" />
                        </td>
                        <td>
                            <time><?= $comment->created->nice(Hash::get($settings, 'timezone')) ; ?></time>
                        </td>
                        <td class="comment-name">
                            <div><?= h($comment->display_name); ?></div>
                            <span class="tag"><?= h($comment->posted_by); ?></span>
                        </td>
                        <td class="comment-comment"><?= nl2br(h($comment->comment)); ?></td>
                        <td>
                            <?php
                                if ($comment->item) {
                                    echo $this->Html->link(
                                        $comment->item->name . '&nbsp;<span class="fas fa-external-link-alt"></span>',
                                        [
                                            '_name' => $comment->onPost ? 'viewPost' : 'viewMedia',
                                            $comment->model_id
                                        ],
                                        [
                                            'target' => '_blank',
                                            'escape' => false,
                                            'class' => 'item-link'
                                        ]
                                    );
                                    echo '<br />';
                                    echo $this->Html->tag('span', $comment->onPost ? 'Post' : 'Media', ['class' => 'tag']);
                                } else {
                                    echo __('N/A');
                                }
                            ?>
                        </td>
                        <td>
                            <ul class="actions">
                                <li>
                                    <a class="approve-comment has-text-success" data-id="<?= $comment->id; ?>" title="<?= __('Approve'); ?>">
                                        <span class="fas fa-check"></span>
                                    </a>
                                </li>
                                <li>
                                    <a class="delete-comment has-text-danger" data-id="<?= $comment->id; ?>" title="<?= __('Delete'); ?>">
                                        <span class="fas fa-trash"></span>
                                    </a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
            echo $this->Form->end();
        }
    ?>
    </div>
</div>
