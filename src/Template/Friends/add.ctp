<?php
$this->assign('title', __('Add Friend'));
?>
<div id="addFriendPage">
    <div class="container">
        <div class="columns">
            <div class="column">
                <h1 class="is-size-2"><?= __('Add Friend'); ?></h1>
                <?php
                    echo $this->Form->create();
                        echo $this->Form->control(
                            'url',
                            [
                                'label' => __('Website URL'),
                                'type' => 'url',
                                'required' => true
                            ]
                        );
                        echo $this->Form->control(
                            'name',
                            [
                                'label' => __('Name'),
                                'type' => 'text',
                                'required' => true
                            ]
                        );
                        echo $this->Form->control(
                            'description',
                            [
                                'label' => __('Description'),
                                'type' => 'textarea'
                            ]
                        );
                        echo $this->Form->control(
                            'feed_url',
                            [
                                'label' => __('Feed URL'),
                                'type' => 'text',
                                'required' => true
                            ]
                        );
                        echo $this->Form->control(
                            'icon',
                            [
                                'label' => __('Icon'),
                                'type' => 'text',
                            ]
                        );
                        echo $this->Form->button(
                            __('Add Friend'),
                            [
                                'type' => 'submit',
                                'class' => 'button is-primary'
                            ]
                        );
                    echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
</div>
