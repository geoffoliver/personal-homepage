<?php

use Cake\Utility\Hash;

$this->assign('title', __('Settings'));
// $this->append('css', $this->Html->css('/js/lib/quill/quill.core.css'));
// $this->append('script', $this->Html->script('posts/add.js'));
?>
<section class="section" id="settings">
    <div class="columns">
        <div class="column is-three-fifths is-offset-one-fifth">
            <h1 class="is-size-3"><?= __('Settings'); ?></h1>
            <div class="box">
                <?php
                    echo $this->Form->create(null, ['type' => 'file']);
                        echo $this->Html->tag('h4', __('General'), ['class' => 'title is-4']);

                        echo $this->Form->input('site-name', [
                            'label' => __('Name'),
                            'type' => 'text',
                            'value' => Hash::get($settings, 'site-name')
                        ]);

                        echo $this->Form->input('site-title', [
                            'label' => __('Title'),
                            'type' => 'text',
                            'value' => Hash::get($settings, 'site-title')
                        ]);

                        echo $this->Form->input('picture', [
                            'label' => __('Picture'),
                            'type' => 'file'
                        ]);

                        if ($picture = Hash::get($settings, 'picture')) {
                            echo $this->Html->tag('figure',
                                $this->Html->image(
                                    $this->Url->build([
                                        'controller' => 'Medias',
                                        'action' => 'download',
                                        $picture,
                                        'square_thumbnail'
                                    ]),
                                    [
                                        'class' => 'site-picture'
                                    ]
                                ),
                                [
                                    'class' => 'image is-96x96'
                                ]
                            );
                        }

                        echo '<hr />';

                        echo $this->Html->tag('h4', __('About'), ['class' => 'title is-4']);

                        echo $this->Form->input('homepage-about', [
                            'label' => __('Homepage blurb/"About" page intro'),
                            'type' => 'text',
                            'value' => Hash::get($settings, 'homepage-about')
                        ]);

                        echo $this->Form->input('about-page', [
                            'label' => __('"About" page'),
                            'type' => 'textarea',
                            'value' => Hash::get($settings, 'about-page')
                        ]);

                        echo '<hr />';

                        echo $this->Html->tag('h4', __('Homepage Cover'), ['class' => 'title is-4']);

                        echo $this->Form->input('cover-title', [
                            'label' => __('Title'),
                            'type' => 'text',
                            'value' => Hash::get($settings, 'cover-title')
                        ]);

                        echo $this->Form->input('cover-subtitle', [
                            'label' => __('Subtitle'),
                            'type' => 'text',
                            'value' => Hash::get($settings, 'cover-subtitle')
                        ]);

                        echo $this->Form->input('cover-photo', [
                            'label' => __('Background Picture'),
                            'type' => 'file'
                        ]);

                        if ($cover = Hash::get($settings, 'cover-photo')) {
                            echo $this->Html->tag('figure',
                                $this->Html->image(
                                    $this->Url->build([
                                        'controller' => 'Medias',
                                        'action' => 'download',
                                        $cover,
                                        'thumbnail'
                                    ]),
                                    [
                                        'class' => 'cover-photo'
                                    ]
                                ),
                                [
                                    'class' => 'image'
                                ]
                            );
                        }

                        echo '<hr />';

                        echo $this->Html->tag('h4', __('Other'), ['class' => 'title is-4']);

                        echo $this->Form->input('timezone', [
                            'label' => __('Timezone'),
                            'type' => 'select',
                            'value' => Hash::get($settings, 'timezone'),
                            'options' => $timezones
                        ]);

                        echo '<hr />';

                        echo $this->Form->button(
                            __('Save Settings'),
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
