<?php
$this->assign('title', __('Edit Friend'));
?>
<div id="addFriendPage">
    <div class="container">
        <div class="columns">
            <div class="column">
                <h1 class="is-size-2"><?= __('Edit Friend'); ?></h1>
                <?php
                    echo $this->Form->create();
                        echo $this->Form->control(
                            'url',
                            [
                                'label' => __('Website URL'),
                                'type' => 'url',
                                'required' => true,
                                'value' => $friend->url
                            ]
                        );
                        echo $this->Form->control(
                            'name',
                            [
                                'label' => __('Name'),
                                'type' => 'text',
                                'required' => true,
                                'value' => $friend->name
                            ]
                        );
                        echo $this->Form->control(
                            'description',
                            [
                                'label' => __('Description'),
                                'type' => 'textarea',
                                'value' => $friend->description
                            ]
                        );
                        echo $this->Form->control(
                            'feed_url',
                            [
                                'label' => __('Feed URL'),
                                'type' => 'text',
                                'required' => true,
                                'value' => $friend->feed_url
                            ]
                        );
                        echo $this->Form->control(
                            'icon',
                            [
                                'label' => __('Icon'),
                                'type' => 'text',
                                'value' => $friend->icon
                            ]
                        );
                        echo $this->Form->button(
                            __('Save Friend'),
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
