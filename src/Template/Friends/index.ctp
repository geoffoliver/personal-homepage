<?php
$this->assign('title', __('Friends'));
$this->assign('css', $this->Html->css('friends/index.css'));
?>
<div id="friendsPage">
    <div class="container">
        <div class="columns">
            <div class="column">
                <h1 class="is-size-2">
                    <?= __('Friends'); ?>
                    <?php if ($user): ?>
                        <?= $this->Html->link(
                            $this->Html->tag('i', '', ['class' => 'fas fa-plus-circle']) . '&nbsp;&nbsp;' . __('Add Friend'),
                            [
                                'action' => 'add'
                            ],
                            [
                                'title' => __('Add Friend'),
                                'class' => 'button is-dark is-size-6',
                                'escape' => false
                            ]
                        ); ?>
                    <?php endif; ?>
                </h1>
                <?php if ($friends): ?>
                    <div class="row">
                        <div class="columns is-multiline">
                            <?php foreach ($friends as $friend): ?>
                                <div class="column is-one-third">
                                    <div class="friends-list-friend">
                                        <div class="friends-list-friend-icon">
                                            <?= $this->Html->tag(
                                                'img',
                                                null,
                                                [
                                                    'data-lazy-src' => $this->Url->build([
                                                        'controller' => 'Friends',
                                                        'action' => 'icon',
                                                        $friend->id
                                                    ]),
                                                    'alt' => "Icon for {$friend->name}",
                                                    'loading' => 'lazy'
                                                ]
                                            ); ?>
                                        </div>
                                        <div class="friends-list-friend-info">
                                            <p>
                                                <strong><?= $friend->name; ?></strong>
                                            </p>
                                            <?= $this->Html->link(
                                                __('View Website'),
                                                $friend->url,
                                                [
                                                    'target' => '_blank',
                                                    'class' => 'is-size-7'
                                                ]
                                            ); ?>
                                        </div>
                                        <?php if ($user): ?>
                                            <div class="friends-list-friend-buttons buttons are-small">
                                                <?= $this->Html->Link(
                                                    __('Edit'),
                                                    [
                                                        'action' => 'edit',
                                                        $friend->id
                                                    ],
                                                    [
                                                        'class' => 'level-item button is-dark is-outlined is-fullwidth',
                                                    ]
                                                ); ?>
                                                <?php
                                                    echo $this->Form->postLink(
                                                        __('Delete'),
                                                        ['action' => 'delete', $friend->id],
                                                        [
                                                            'confirm' => __('Are you sure you want to delete this friend?'),
                                                            'class' => 'button is-danger is-outlined is-fullwidth',
                                                            'type' => 'submit'
                                                        ]
                                                    );
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <?php if ($user): ?>
                        <div class="message is-dark">
                            <div class="message-body">
                                <?= __('You have not setup any friends yet.'); ?>
                                <br><br>
                                <?= $this->Html->link(
                                    __('Add Friend'),
                                    [
                                        'action' => 'add'
                                    ],
                                    [
                                        'class' => 'button is-dark'
                                    ]
                                ); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="message is-dark">
                            <div class="message-body">
                                <?= __('There are no friends to display.'); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
