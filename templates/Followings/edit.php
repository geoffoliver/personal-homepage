<?php
$this->assign('title', __('Edit Follow'));
$this->assign('css', $this->Html->css('followings/add.css'));
?>
<section class="section" id="editFollowPage">
    <div class="container">
        <div class="columns">
            <div class="column is-three-fifths is-offset-one-fifth">
                <h1 class="is-size-3"><?= __('Edit Follow'); ?></h1>
                <?php
                    echo $this->Form->create();
                        echo $this->Form->control(
                            'url',
                            [
                                'label' => __('Website URL'),
                                'type' => 'url',
                                'required' => true,
                                'value' => $following->url
                            ]
                        );
                        echo $this->Form->control(
                            'name',
                            [
                                'label' => __('Name'),
                                'type' => 'text',
                                'required' => true,
                                'value' => $following->name
                            ]
                        );
                        // echo $this->Form->control(
                        //     'description',
                        //     [
                        //         'label' => __('Description'),
                        //         'type' => 'textarea',
                        //         'value' => $following->description
                        //     ]
                        // );
                        echo $this->Form->control(
                            'feed_url',
                            [
                                'label' => __('Feed URL'),
                                'type' => 'text',
                                'required' => true,
                                'value' => $following->feed_url
                            ]
                        );
                        echo $this->Form->control(
                            'icon',
                            [
                                'label' => __('Icon'),
                                'type' => 'text',
                                'value' => $following->icon
                            ]
                        );
                        echo $this->Form->button(
                            __('Save Follow'),
                            [
                                'type' => 'submit',
                                'class' => 'button is-dark'
                            ]
                        );
                    echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
</section>
