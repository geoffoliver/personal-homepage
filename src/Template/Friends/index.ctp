<?php
$this->assign('title', __('Friends'));
$this->assign('css', $this->Html->css('friends-list.css'));
?>
<div id="friendsPage">
    <div class="container">
        <div class="columns">
            <div class="column">
                <h1 class="is-size-2">
                    <?= __('Friends'); ?>
                    <?php if ($user): ?>
                        <?= $this->Html->link(
                            $this->Html->tag('i', '', ['class' => 'fas fa-plus-circle']),
                            [
                                'action' => 'add'
                            ],
                            [
                                'title' => __('Add Friend'),
                                'class' => 'is-size-6',
                                'escape' => false
                            ]
                        ); ?>
                    <?php endif; ?>
                </h1>
                <?php if ($friends->count()): ?>
                    <div class="box">
                        <div class="row">
                            <div class="columns is-multiline">
                                <?php foreach ($friends as $friend): ?>
                                    <div class="column is-one-third">
                                        <div class="friends-list-friend">
                                            <div class="friends-list-friend-icon">
                                                <?= $this->Html->image(
                                                    $friend->icon,
                                                    [
                                                        'alt' => "Icon for {$friend->name}"
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
                                                            'class' => 'level-item button is-link is-fullwidth',
                                                        ]
                                                    ); ?>
                                                    <?php
                                                        echo $this->Form->create(
                                                            null,
                                                            [
                                                                'url' => [
                                                                    'action' => 'delete',
                                                                    $friend->id
                                                                ],
                                                                'class' => 'level-item'
                                                            ]
                                                        );
                                                            echo $this->Form->button(
                                                                __('Delete'),
                                                                [
                                                                    'class' => 'button is-danger is-outlined is-fullwidth',
                                                                    'type' => 'submit'
                                                                ]
                                                            );
                                                        echo $this->Form->end();
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php if ($user): ?>
                        <div class="message is-info">
                            <div class="message-body">
                                <?= __('You have not setup any friends yet.'); ?>
                                <br><br>
                                <?= $this->Html->link(
                                    __('Add Friend'),
                                    [
                                        'action' => 'add'
                                    ],
                                    [
                                        'class' => 'button is-link'
                                    ]
                                ); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="message is-info">
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
