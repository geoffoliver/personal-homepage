<?php
$this->assign('title', __('Following'));
$this->assign('css', $this->Html->css('followings/index.css'));
?>
<section class="section" id="followingsPage">
    <div class="columns">
        <div class="column">
            <h1 class="title">
                <?= __('Following'); ?>
                <?php if ($user): ?>
                    <?= $this->Html->link(
                        $this->Html->tag('i', '', ['class' => 'fas fa-plus-circle']) . '&nbsp;&nbsp;' . __('Add Follow'),
                        [
                            'action' => 'add'
                        ],
                        [
                            'title' => __('Add Follow'),
                            'class' => 'button is-dark is-size-6',
                            'escape' => false
                        ]
                    ); ?>
                <?php endif; ?>
            </h1>
            <?php if ($followings): ?>
                <div class="row">
                    <div class="columns is-multiline">
                        <?php foreach ($followings as $following): ?>
                            <div class="column is-half">
                                <div class="followings-list-following">
                                    <div class="followings-list-following-icon">
                                        <?= $this->Html->tag(
                                            'img',
                                            null,
                                            [
                                                'src' => $this->Url->build([
                                                    'controller' => 'Followings',
                                                    'action' => 'icon',
                                                    $following->id
                                                ]),
                                                'alt' => "Icon for {$following->name}",
                                                'loading' => 'lazy'
                                            ]
                                        ); ?>
                                    </div>
                                    <div class="followings-list-following-info">
                                        <p>
                                            <strong><?= $following->name; ?></strong>
                                        </p>
                                        <?= $this->Html->link(
                                            __('View Website'),
                                            $following->url,
                                            [
                                                'target' => '_blank',
                                                'class' => 'is-size-7'
                                            ]
                                        ); ?>
                                    </div>
                                    <?php if ($user): ?>
                                        <div class="followings-list-following-buttons buttons are-small">
                                            <?= $this->Html->Link(
                                                __('Edit'),
                                                [
                                                    'action' => 'edit',
                                                    $following->id
                                                ],
                                                [
                                                    'class' => 'level-item button is-dark is-outlined is-fullwidth',
                                                ]
                                            ); ?>
                                            <?php
                                                echo $this->Form->postLink(
                                                    __('Delete'),
                                                    ['action' => 'delete', $following->id],
                                                    [
                                                        'confirm' => __('Are you sure you want to delete this following?'),
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
                            <?= __('You aren\'t following anybody!'); ?>
                            <br><br>
                            <?= $this->Html->link(
                                __('Add Follow'),
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
                            <?= __('I\'m not following anybody just yet.'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</seciton>
